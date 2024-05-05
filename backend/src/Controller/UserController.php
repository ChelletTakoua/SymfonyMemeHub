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


class UserController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(User::class);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(#[CurrentUser] ?User $user): Response
    {
        return $this->json(['user' => $user,'memes' => $user->getMemes()], Response::HTTP_OK); 
    }

    #[Route('/forgotPassword/{username}', name: 'forgot_password')]
    public function forgotPassword($username): Response
    {
        $user = $this->repo->findOneBy(['username' => $username]);
        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }
        //Mail Service
        return new Response('');
    }
    // #[Route('/resetPassword', name: 'reset_password')]
    // public function resetPassword(): Response
    // {
    //     // Your code here
    //     return new Response('');
    // }

    #[Route('/sendVerificationEmail/{username}', name: 'send_verification_email')]
    public function sendVerificationEmail($username): Response
    {
        $user = $this->repo->findOneBy(['username' => $username]);
        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }
        //Mail Service
        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }

    // #[Route('/verifyEmail', name: 'verify_email')]
    // public function verifyEmail(): Response
    // {
    //     // Your code here
    //     return new Response('');
    // }

    #[Route('/user/{id}', name: 'get_user_profile')]
    public function getUserProfile($id): Response
    {
        $user = $this->repo->find($id);
        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }
        return $this->json(['user' => $user]);
    }

    // #[Route('/user/profile/modifyPassword', name: 'modify_password')]
    // public function modifyPassword(): Response
    // {
    //     // Your code here
    // }

    #[Route('/user/profile/edit', name: 'edit_profile',  methods: ['POST'])]
    public function editProfile(Request $request, ?User $user): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!empty($data) && (isset($data['username']) || isset($data['email']) || isset($data['profile_pic']))) {
            $id = $user->getId();
            $user = $this->repo->find($id);
            if (isset($data['username'])) {
                $username = $data['username'];
                $user->setUsername($username);
            }
            if (isset($data['email'])) {
                $email = $data['email'];
                $user->setEmail($email);
            }
            if (isset($data['profile_pic'])) {
                $profile_pic = $data['profile_pic'];
                $user->setProfilePic($profile_pic);
            }
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->json(["user" => $user]);
        } else {
            throw new BadRequestHttpException("A parameter must be provided");
        }
    }

    #[Route('/user/profile', name: 'delete_profile')]
    public function deleteProfile(): Response
    {
        // Your code here
        // the user will be banned not removed from the db
        return new Response('');
    }
}
