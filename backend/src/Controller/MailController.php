<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerService;

class MailController extends AbstractController
{
    #[Route('/mail', name: 'app_mail')]
    public function index(MailerService $mailer): Response
    {
        $mailer->sendEmail(content:'aman tibaathhhhhhh');
        return $this->render('mail/index.html.twig');
    }
}
