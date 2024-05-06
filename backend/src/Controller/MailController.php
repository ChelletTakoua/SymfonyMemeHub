<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerService;
use App\Entity\User;
 

class MailController extends AbstractController
{
    #[Route('/mail', name: 'app_mail')]
    public function index(MailerService $mailer): Response
    {
        $user=new User();
        $user->setEmail('taki74ayadi@gmail.com');
        $user->setUsername('taki');
        $mailer->sendAccountCreatedMail($user);
        $mailer->sendPasswordResetMail($user);
        return $this->render('mail/index.html.twig');
    }
}