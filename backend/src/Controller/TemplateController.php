<?php

namespace App\Controller;

use App\Entity\Template;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TemplateController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;
    private $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(Template::class);
        $this->em = $this->doctrine->getManager();
    }

    #[Route('/templates', name: 'get_all_templates')]
    public function getAllTemplates(): Response
    {
        $templates = $this->repo->findAll();
        return $this->json(["templates" => $templates]);
    }

    #[Route('/templates/{id}', name: 'get_template_by_id')]
    public function getTemplateById(?Template $template=null): Response
    {
        if (!$template) {
            throw new NotFoundHttpException("Template not found");
        }
        return $this->json($template);
    }

    #[Route('/admin/templates/url', name: 'get_template_by_url')]
    public function getTemplateByUrl(Request $request): Response
    {
        $url = $request->toArray()['url'] ?? null;
        $template = $this->repo->findOneBy(['URL' => $url]);
        if (!$template) {
            throw new NotFoundHttpException("Template not found");
        }
        return $this->json($template);
    }

    // TODO : if the template is used in a meme, it should not be deleted
    //              and the user should be notified that the template is in use ( Raise a BadRequestHttpException )
    #[Route('/templates/delete/{id}', name: 'delete_template')]
    public function deleteTemplate(?Template $template=null): Response
    {
        if (!$template) {
            throw new NotFoundHttpException("Template not found");
        }

        if ($template->getMemes()->count() > 0) {
            throw new BadRequestHttpException("Template is in use");
        }

        $this->em->remove($template);
        $this->em->flush();
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
