<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\MailerService;
use App\Service\AuthKeyService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Token\JWTUserToken;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;
    private $mailer;
    private $jwtService;
    private $auth;

    public function __construct(ManagerRegistry $doctrine, MailerService $mailer, AuthKeyService $jwtService, AuthController $auth)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(User::class);
        $this->mailer = $mailer;
        $this->jwtService = $jwtService;
        $this->auth = $auth;
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        if(!$user){
            return new JsonResponse(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        return $this->json(['user' => $user,
                                    'memes' => $user->getMemes()],
                                    Response::HTTP_OK);
    }

    #[Route('/user/{id}', name: 'get_user_profile')]
    public function getUserProfile(?User $user=null): JsonResponse
    {
        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }

        return $this->json(['user' => $user]);
    }



   #[Route('/forgotPassword/{username}', name: 'forgot_password')]
   public function forgotPassword($username)
   {
       $user = $this->repo->findOneBy(['username' => $username]);
       if (!$user) {
           throw new NotFoundHttpException("User not found");
       }
       //Mail Service
       $this->mailer->sendPasswordResetMail($user);
       $emailParts = explode('@', $user->getEmail());
        $hiddenEmailPart = substr($emailParts[0], 0, 2) . str_repeat('*', strlen($emailParts[0]) - 2);
        $hiddenEmail = $hiddenEmailPart . '@' . $emailParts[1];
        return new JsonResponse(["email" => $hiddenEmail]);
    }

   #[Route('/sendVerificationEmail/{username}', name: 'send_verification_email')]
    public function sendVerificationEmail($username) : JsonResponse
   {
        $user = $this->repo->findOneBy(['username' => $username]);
        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }
        if($user->isVerified()){
            return new JsonResponse(['message' => 'User is already verified'], Response::HTTP_OK);
        }
        $this->mailer->sendAccountCreatedMail($user);
       $emailParts = explode('@', $user->getEmail());
       $hiddenEmailPart = substr($emailParts[0], 0, 2) . str_repeat('*', strlen($emailParts[0]) - 2);
       $hiddenEmail = $hiddenEmailPart . '@' . $emailParts[1];
       return new JsonResponse(["email" => $hiddenEmail,'status' => 'Email sent']);
    }

    #[Route('/verifyEmail', name: 'verify_email')]
    public function verifyEmail(EntityManagerInterface $entityManager, Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['token'])) {
            throw new BadRequestHttpException("Token must be provided");
        }

        $token = $data['token'];
        try {
            $user = $this->jwtService->decodeJWT($token);
            $user->setVerified(true);
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            throw new BadRequestHttpException("Invalid token");
        }
        return new JsonResponse(['status' => 'Email verified'], Response::HTTP_CREATED);
    }

    #[Route('/resetPassword', name: 'reset_password')]
    public function resetPassword(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $data = $request->toArray();
       
        if (!isset($data['token'])) {
            throw new BadRequestHttpException("Token must be provided");
        }
        if (!isset($data['password'])) {
            throw new BadRequestHttpException("Password must be provided");
        }

        $user = $this->jwtService->decodeJWT($data['token']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse(['status' => 'Password changed'], Response::HTTP_CREATED);
        //$this->auth->login($user->getUsername(), $password, false);
    }

    #[Route('/user/profile/edit', name: 'edit_profile',  methods: ['POST'])]
    public function editProfile(Request $request): Response
    {
        $user= $this->getUser();
        $data = $request->toArray();
        if (!empty($data) && (isset($data['username']) || isset($data['email']) || isset($data['profilePic']))) {
            if (isset($data['username'])) {
                $username = $data['username'];
                $user->setUsername($username);
            }
            if (isset($data['email'])) {
                $email = $data['email'];
                $user->setEmail($email);
            }
            if (isset($data['profilePic'])) {
                $profilePic = $data['profilePic'];
                $profilePicBlob = fopen('data://text/plain;base64,' . base64_encode($profilePic), 'r');
                $user->setProfilePic($profilePicBlob);
            }
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->json(["user" => $user]);
        }

        throw new BadRequestHttpException("A parameter must be provided");
    }

    #[Route('/deleteAccount', name: 'delete_profile', methods: ['DELETE'])]
    public function deleteProfile(): Response
    {
        $user = $this->getUser();
        $user->softDelete($this->doctrine->getManager());
        return new Response('');
    }
}
