<?php

namespace App\Controller;

use App\Entity\BannedUser;
use App\Entity\Meme;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BannedUserController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(BannedUser::class);
    }

    #[Route('/banned/user', name: 'app_banned_user')]
    public function index(): Response
    {
        $this->repo->findByTerm('user');
        return $this->render('banned_user/index.html.twig', [
            'controller_name' => 'BannedUserController',
        ]);
    }
}
