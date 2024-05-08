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

    // TODO: when the user is banned, the user should not be able to login or access any routes
    //              and his memes should become blocked till the ban duration is over
    //             if the user is banned permanently, the ban duration should be set to null

}
