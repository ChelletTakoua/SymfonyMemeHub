<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Report;
use App\Entity\BlockedMeme;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
class ReportController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(Report::class);
    }
    /**
     * Get all reports in the database
     */

    #[Route('/admin/reports', name: 'get_all_reports')]
    public function getAllReports(): Response
    {
        $reports = $this->repo->findAll();
        return $this->json(["reports" => $reports]);
    }

     /**
     * Resolve a report (changes the status to resolved and blocks the meme)
     * @param $id - the id of the report
     * @param $admin - the admin that resolved the report
     * @return Response
     */

    #[Route('/admin/reports/{id}/resolve', name: 'resolve_report')]
    public function resolveReport(?Report $report): Response
    {
        $admin = $this->getUser();
        if (!$report) {
            throw new NotFoundHttpException("Report not found");
        }
        $report->setStatus('resolved');
        $blockedMeme = new BlockedMeme();
        $blockedMeme->setMeme($report->getMeme());
        $blockedMeme->setAdmin($admin);
        $blockedMeme->addReportId($report);
        $em = $this->doctrine->getManager();
        $em->persist($report);
        $em->persist($blockedMeme);
        $em->flush();
        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }
    /**
     * Ignore a report (changes the status to ignored and unblocks the meme)
     * @param $id - the id of the report
     * @param $admin - the admin that resolved the report
     * @return Response
     */

    #[Route('/admin/reports/{id}/ignore', name: 'ignore_report')]
    public function ignoreReport(Report $report): Response
    {
        $report->setStatus('ignored');
       
        $blockedMeme = $report->getBlockedMeme();
        $report->setBlockedMeme(null);
        $entityManager = $this->doctrine->getManager();
        if ($blockedMeme) {
            $entityManager->remove($blockedMeme);
        }
        $entityManager->persist($report);
        $entityManager->flush();
        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }

}





     
   

   
   