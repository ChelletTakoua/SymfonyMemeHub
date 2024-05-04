<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebuggingController extends AbstractController
{
    #[Route('/debugging', name: 'app_debugging')]
    public function index(): Response
    {
        return $this->render('debugging/index.html.twig', [
            'controller_name' => 'DebuggingController',
        ]);
    }

    #[Route('/sessionHistory', name: 'app_session_history')]
    public function sessionHistory(): Response
    {
        return $this->render('debugging/sessionHistory.html.twig', [
            'controller_name' => 'DebuggingController',
        ]);
    }

#[Route('/requestDetails', name: 'app_request_details')]
    public function requestDetails(): Response
    {
        return $this->render('debugging/requestDetails.html.twig', [
            'controller_name' => 'DebuggingController',
        ]);
    }

    #[Route('/responseDetails', name: 'app_response_details')]
    public function responseDetails(): Response
    {
        return $this->render('debugging/responseDetails.html.twig', [
            'controller_name' => 'DebuggingController',
        ]);
    }
}
