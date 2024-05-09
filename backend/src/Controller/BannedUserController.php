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
    public function banUser(Request $request, ?User $user = null): Response
    {
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }
        if ($user->isBanned()) {
            throw new BadRequestHttpException('User is already banned');
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
        $banEndDate = new \DateTime($requestBody['banEndDate']);
        if ($banEndDate < new \DateTime()) {
            throw new BadRequestHttpException("BanEndDate can't be in the past");
        }
        $bannedUser = new BannedUser();
        $bannedUser->setUser($user);

        $bannedUser->setAdmin($admin);

        $reason = $requestBody['reason'];


        $banDuration = null;
        if ($banEndDate) {
            $now = new \DateTime();
            $banDuration = $banEndDate->diff($now)->days;
        }
        $bannedUser->setBanDuration($banDuration);
        $bannedUser->setBanEndDate($banEndDate);
        $bannedUser->setReason($reason);

        $entityManager = $this->doctrine->getManager();
        $user->setBanned(true);
        $entityManager->persist($bannedUser);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }
}
