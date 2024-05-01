<?php

namespace App\Controller;

use App\Entity\Template;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TemplateController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(Template::class);
    }

    #[Route('/templates', name: 'get_all_templates')]
    public function getAllTemplates(): Response
    {
        $templates = $this->repo->findAll();
        return $this->json(["templates" => $templates]);
    }

    #[Route('/templates/{id}', name: 'get_template_by_id')]
    public function getTemplateById($id): Response
    {
        $template = $this->repo->findOneBy(['id' => $id]);
        if (!$template) {
            throw new NotFoundHttpException("Template not found");
        }
        return $this->json($template);
    }

    #[Route('/admin/templates/url/{url}', name: 'get_template_by_url')]
    public function getTemplateByUrl(String $url): Response
    {
        $template = $this->repo->findOneBy(['URL' => $url]);
        if (!$template) {
            throw new NotFoundHttpException("Template not found");
        }
        return $this->json($template);
    }

    #[Route('/templates/{id}/delete', name: 'delete_template')]
    public function deleteTemplate($id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw new NotFoundHttpException("User not logged in");
        }
        $template = $this->repo->find($id);
        if (!$template) {
            throw new NotFoundHttpException("Template not found");
        }
        if ($template->getUser() !== $user) {
            throw new AccessDeniedHttpException("No permission to delete this Template");
        }
        $em = $this->doctrine->getManager();
        $em->remove($template);
        $em->flush();
        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }

    // #[Route('/templates/add', name: 'add_template')]
    // public function addTemplate(): Response
    // {
    //     return $this->json([
    //         'status' => 'success',
    //         'code' => 200
    //     ]);
    // }
}
