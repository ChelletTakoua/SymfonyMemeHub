<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = $request->toArray();
        $user = new User();

        if (empty($data['email']) || empty($data['username']) || empty($data['password'])) {
            return new JsonResponse(['message' => 'Email, username and password are required'], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Email already used'], Response::HTTP_BAD_REQUEST);
        }
        $user->setEmail($data['email']);
    
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Username already used'], Response::HTTP_BAD_REQUEST);
        }
        $user->setUsername($data['username']);

        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse(['status' => 'User created!'], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(SessionInterface $session): JsonResponse
    {
        $user= $this->getUser();
        if($user->isBanned()) {
            $session->invalidate();
            return new JsonResponse(['error' => 'User is banned'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->isVerified()) {
            $session->invalidate();
            return new JsonResponse(['error' => 'User is not verified'], Response::HTTP_FORBIDDEN);
        }
        return new JsonResponse(['user' => $user ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): JsonResponse
    {
        return new JsonResponse(['status' => 'Logged out'], Response::HTTP_OK);
    }

    #[Route('/check_auth', name: 'check_auth')]
    public function check_auth(): JsonResponse
    {
        $user = $this->getUser();
        if ($user) {
            return new JsonResponse(['user' => $user], Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }

    #[Route('/access_denied', name: 'denied')]
    public function denied(): JsonResponse
    {
        return new JsonResponse(['message' => 'Access denied'], Response::HTTP_UNAUTHORIZED);
    }


}
