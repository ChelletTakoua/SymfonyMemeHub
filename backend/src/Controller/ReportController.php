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

class ReportController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(Report::class);
    }


    #[Route('/admin/reports', name: 'get_all_reports')]
    public function getAllReports(): Response
    {
        $reports = $this->repo->findAll();
        return $this->json(["reports" => $reports]);
    }

    #[Route('/admin/reports/{id}/resolve', name: 'resolve_report')]
    public function resolveReport($id, ?User $admin): Response
    {
        $id = 4;
        $report = $this->repo->find($id);
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

    #[Route('/admin/reports/{id}/ignore', name: 'ignore_report')]
    public function ignoreReport($id): Response
    {
        $report = $this->doctrine->getRepository(Report::class)->find($id);
        if (!$report) {
            throw new NotFoundHttpException("Report not found");
        }
        $report->setStatus('ignored');
        //fetch meme having reportId = $id
        $blockedMeme = $query->getOneOrNullResult();
        if ($blockedMeme) {
            $this->doctrine->getManager()->remove($blockedMeme);
        }
        $this->doctrine->getManager()->flush();
        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }

    #[Route('/admin/reports/{id}/delete', name: 'delete_report')]
    public function deleteReport($id): Response
    {
        $report = $this->repo->find($id);
        if (!$report) {
            throw new NotFoundHttpException("Report not found");
        }
        $em = $this->doctrine->getManager();
        $em->remove($report);
        $em->flush();
        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }
}
