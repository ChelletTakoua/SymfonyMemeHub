<?php

namespace App\Controller;

use App\Entity\Meme;
use App\Entity\User;
use App\Entity\BannedUser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BannedUserController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(BannedUser::class);
    }

    #[Route('/admin/ban/{id}', name: 'ban_user')]
    public function banUser(?User $user = null, Request $request): Response
    {
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $admin = $this->getUser();

        $requestBody = $request->toArray() ?? [];
        if (empty($requestBody)) {
            throw new BadRequestHttpException('Request body empty');
        }
        if (!isset($requestBody['reason'])) {
            throw new BadRequestHttpException("Reason can't be empty");
        }
        if (!isset($requestBody['banEndDate'])) {
            throw new BadRequestHttpException("BanEndDate can't be empty");
        }

        $bannedUser = new BannedUser();
        $bannedUser->setUser($user);
        $bannedUser->setAdmin($admin);

        $reason = $requestBody['reason'];
        $banEndDate = new \DateTime($requestBody['banEndDate']);

        $banDuration = null;
        if ($banEndDate) {
            $now = new \DateTime();
            $banDuration = $banEndDate->diff($now)->days;
        }
        $bannedUser->setBanDuration($banDuration);
        $bannedUser->setReason($reason);

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($bannedUser);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }

    // TODO: when the user is banned, the user should not be able to login or access any routes

}
