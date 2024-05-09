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
        $response = $this->json(["email" => $hiddenEmail]);
        dd($response);
    }

    #[Route('/resetPassword', name: 'reset_password')]
    public function resetPassword(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher)
    {   
        $this->mailer->sendAccountCreatedMail($this->getUser());
        $data = $request->toArray();
       
        if (!isset($data['token'])) {
            throw new BadRequestHttpException("Token must be provided");
        }
        if (!isset($data['password'])) {
            throw new BadRequestHttpException("Password must be provided");
        }

        $token = $data['token'];
        $password = $data['password'];
        $user = $this->jwtService->decodeJWT($token);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse(['status' => 'Password changed'], Response::HTTP_CREATED);
        //$this->auth->login($user->getUsername(), $password, false);
        dd($user);
    }




   

    // }
//    #[Route('/sendVerificationEmail/{username}', name: 'send_verification_email')]
//    public function sendVerificationEmail($username): Response
// {
//        $user = $this->repo->findOneBy(['username' => $username]);
//        if (!$user) {
//            throw new NotFoundHttpException("User not found");
//        }
//        //Mail Service
//        return $this->json([
//            'status' => 'success',
//            'code' => 200
//        ]);
//    }

    // #[Route('/verifyEmail', name: 'verify_email')]
    // public function verifyEmail(): Response
    // {
    //     // Your code here
    //     return new Response('');
    // }


    #[Route('/user/{id}', name: 'get_user_profile')]
    public function getUserProfile(?User $user=null): JsonResponse
    {
        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }

        return $this->json(['user' => $user]);
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

    #[Route('/user/profile', name: 'delete_profile', methods: ['DELETE'])]
    public function deleteProfile(): Response
    {
        dd($this->mailer);
        // Your code here
        // the user will be banned not removed from the db
        return new Response('');
    }
}
