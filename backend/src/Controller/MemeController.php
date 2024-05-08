<?php

namespace App\Controller;


use App\Entity\Like;
use App\Entity\Meme;
use App\Entity\User;
use App\Entity\Report;
use App\Entity\Template;
use App\Entity\TextBlock;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException; // Import the missing Template class
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Config\Framework\HttpClient\DefaultOptions\RetryFailedConfig;

class MemeController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(Meme::class);
    }

    #[Route('/memes', name: 'get_all_memes', methods: ['GET'])]
    public function getAllMemes(Request $request): Response
    {
        $user = $this->getUser();
        $page = (int)($request->query->get('page') ?? 1);
        $pageSize = (int)($request->query->get('pageSize') ?? -1);
        $memes = $this->repo->findPaginated($page, $pageSize);
        foreach ($memes as &$meme) {
            $isLikedByCurrentUser = false;
            if ($user) {
                $isLikedByCurrentUser = $this->repo->isLikedByUser($meme, $user);
            }
            $meme['liked'] = $isLikedByCurrentUser;
        }

        $result = [
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => count($memes),
            'totalPages' => $this->repo->getTotalPages($pageSize),
            'memes' => $memes,
        ];
        return $this->json($result);
    }

    #[Route('/memes/add', name: 'add_meme')]
    public function addMeme(Request $request): Response
    {
        $user = $this->getUser();
        $requestBody = $request->toArray() ?? [];
        if (!$user) {
            throw new NotFoundHttpException('User not logged in');
        }
        if (empty($requestBody) || !isset($requestBody['template_id']) || !isset($requestBody['text_blocks']) || !isset($requestBody['result_img'])) {
            throw new BadRequestHttpException('Invalid request body');
        }
        $em = $this->doctrine->getManager();
        $template = $em->getRepository(Template::class)->find($requestBody['template_id']);
        if (!$template) {
            throw new NotFoundHttpException('Template not found');
        }
        $meme = new Meme();
        $meme->setUser($user);
        $meme->setTemplate($template);

        $resultImg = fopen('data://text/plain;base64,' . base64_encode($requestBody['result_img']), 'r');
        $meme->setResultImg($resultImg);

        $em->persist($meme);
        foreach ($requestBody['text_blocks'] as $textBlock) {
            $tb = new TextBlock();
            $tb->setText($textBlock['text']);
            $tb->setX($textBlock['x']);
            $tb->setY($textBlock['y']);
            $tb->setFontSize($textBlock['font_size']);
            $tb->setMeme($meme);
            $em->persist($tb);
        }
        $em->flush();
        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }

    #[Route('/memes/{id}', name: 'get_meme_byId')]
    public function getMemeById(?Meme $meme=null): Response
    {

        if (!$meme) {
            throw new NotFoundHttpException("Meme not found");
        }
        //$meme = $this->repo->findPaginated($id,5);
        return $this->json($meme->jsonSerialize($this->getUser()));
    }

    #[Route('/memes/user/{id}', name: 'get_user_memes')]
    public function getUserMemes(?User $user=null): Response
    {
        $memes=$this->repo->findMemesByUser($user->getId(),false);
        return $this->json($memes);
    }

    #[Route('/memes/{id}/likes', name: 'get_meme_nb_likes')]
    public function getMemeNbLikes(Meme $meme): Response
    {
        $user = $this->getUser();
        $nbLikes = count($meme->getLikes());
        $isLiked = false;
        $likes = $user->getLikes();

        // add isLiked filed only if user is logged in
        if($user){
            foreach ($likes as $like) {
                if ($like->getMeme() === $meme && $like->getUser() === $user){
                    $isLiked = true;
                    break;
                }
            }
            $response = ['nbLikes' => $nbLikes, 'liked' => $isLiked];
        }else{
            $response = ['nbLikes' => $nbLikes];
        }
        return $this->json($response);
    }


    #[Route('/memes/{id}/modify', name: 'modify_meme')]
    public function modifyMeme(Request $request, $id): Response
    {
        $user = $this->getUser();
        $requestBody = $request->toArray() ?? [];
        if (!$user) {
            throw new NotFoundHttpException('User not logged in');
        }
        if (empty($requestBody) ||  !isset($requestBody['result_img']) || !isset($requestBody['text_blocks'])) {
            throw new BadRequestHttpException('Invalid request body');
        }
        $em = $this->doctrine->getManager();
        $textBlocks = $em->getRepository(TextBlock::class)->findBy(['meme' => $id]);
        foreach ($textBlocks as $textBlock) {
            $em->remove($textBlock);
        }
        foreach ($requestBody['text_blocks'] as $textBlockData) {
            $textBlock = new TextBlock();
            $textBlock->setText($textBlockData['text']);
            $textBlock->setX($textBlockData['x']);
            $textBlock->setY($textBlockData['y']);
            $textBlock->setFontSize($textBlockData['font_size']);
            $textBlock->setMeme($id);
            $em->persist($textBlock);
        }
        $meme = $this->repo->find($id);
        if (!$meme) {
            throw new NotFoundHttpException('Meme not found');
        }
        $meme->setResultImg($requestBody['result_img']);
        $em->persist($meme);
        $em->flush();
        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }

    #[Route('/memes/{id}/like', name: 'like_meme')]
    public function likeMeme($id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw new NotFoundHttpException("User not logged in");
        }
        $meme = $this->repo->find($id);
        if (!$meme) {
            throw new NotFoundHttpException("Meme not found");
        }
        $likeRepo = $this->doctrine->getRepository(Like::class);
        $like = $likeRepo->findOneBy(['meme' => $meme, 'user' => $user]);
        if ($like) {
            $response = ['message' => 'Meme already liked', 400, 'nbLikes' => count($meme->getLikes()), 'liked' => true];
            return $this->json($response);
        }
        $like = new Like();
        $like->setMeme($meme);
        $like->setUser($user);
        $em = $this->doctrine->getManager();
        $em->persist($like);
        $em->flush();
        return $this->json(['nbLikes' => count($meme->getLikes()), 'liked' => true]);
    }

    #[Route('/memes/{id}/dislike', name: 'dislike_meme')]
    public function dislikeMeme($id): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw new NotFoundHttpException("User not logged in ");
        }
        $meme = $this->repo->find($id);
        if (!$meme) {
            throw new NotFoundHttpException("Meme not found");
        }
        $likeRepo = $this->doctrine->getRepository(Like::class);
        $like = $likeRepo->findOneBy(['meme' => $meme, 'user' => $user]);
        if (!$like) {
            return $this->json(['message' => 'Meme not liked yet', 'nbLikes' => count($meme->getLikes()), 'liked' => false], 400);
        }
        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($like);
        $entityManager->flush();
        return $this->json(['nbLikes' => count($meme->getLikes()), 'liked' => false]);
    }

    #[Route('/memes/{id}/report', name: 'report_meme')]
    public function reportMeme($id, Request $request): Response
    {
        $user = $this->getUser();
        $requestBody = $request->toArray() ?? [];
        if (!$user) {
            throw new NotFoundHttpException('User not logged in');
        }
        if (empty($requestBody) || !isset($requestBody['report_reason'])) {
            throw new BadRequestHttpException('Invalid request body');
        }
        $meme = $this->repo->find($id);
        if (!$meme) {
            throw new NotFoundHttpException("Meme not found");
        }
        $reportRepo = $this->doctrine->getRepository(Report::class);
        $report = $reportRepo->findOneBy(['meme' => $meme, 'user' => $user]);
        if ($report) {
            return $this->json(['message' => 'Meme already reported', 'nbReports' => count($meme->getReports())], 400);
        }
        $report = new Report();
        $report->setMeme($meme);
        $report->setUser($user);
        $report->setReason($requestBody['report_reason']);
        $em = $this->doctrine->getManager();
        $em->persist($report);
        $em->flush();
        return $this->json(["report" => $report]);
    }


    #[Route('/memes/{id}/delete', name: 'delete_meme')]
    public function deleteMeme(?Meme $meme): Response
    {
        $user = $this->getUser();
        if (!$meme) {
            throw new NotFoundHttpException("Meme not found");
        }
        if ($meme->getUser() !== $user) {
            throw new AccessDeniedHttpException("No permission to delete this meme");
        }

        $meme->softDelete($this->doctrine->getManager());

        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }
}
