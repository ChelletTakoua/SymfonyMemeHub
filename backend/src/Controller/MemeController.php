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

    #[Route('/memes', name: 'get_all_memes')]
    public function getAllMemes(): Response
    {
        $memes =  $this->repo->findAll();
        usort($memes, function ($a, $b) {
            return $b->getCreationDate() <=> $a->getCreationDate();
        });
        return $this->json($memes);
    }
    #[Route('/memes/add', name: 'add_meme')]
    public function addMeme(Request $request, ?User $user): Response
    {
        $requestBody = json_decode($request->getContent(), true) ?? [];
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
        $meme->setResultImg($requestBody['result_img']);
        $meme->setCreationDate(new \DateTime());
        $meme->setNumLikes(0);
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
    public function getMemeById($id): Response
    {
        $meme = $this->repo->findOneBy(['id' => $id]);
        if (!$meme) {
            throw new NotFoundHttpException("Meme not found");
        }
        return $this->json($meme);
    }

    #[Route('/memes/user/{id}', name: 'get_user_memes')]
    public function getUserMemes($id): Response
    {
        $user = $this->doctrine->getRepository(User::class)->find($id);
        $memes = $this->repo->findBy(['user' => $user]);
        usort($memes, function ($a, $b) {
            return $b->getCreationDate() <=> $a->getCreationDate();
        });
        return $this->json($memes);
    }

    #[Route('/memes/{id}/likes', name: 'get_meme_nb_likes')]
    public function getMemeNbLikes($id, ?User $user): Response
    {
        $meme = $this->repo->findOneBy(['id' => $id]);
        if (!$meme) {
            throw new NotFoundHttpException("Meme not found");
        }
        if (!$user) {
            throw new NotFoundHttpException("User not logged in");
        }
        $nbLikes = count($meme->getLikes());
        $isLiked = false;
        $likes = $user->getLikes();
        foreach ($likes as $like) {
            if ($like->getMeme() === $meme) {
                $isLiked = true;
                break;
            }
        }
        $response = ['nbLikes' => $nbLikes, 'liked' => $isLiked];
        return $this->json($response);
    }


    #[Route('/memes/{id}/modify', name: 'modify_meme')]
    public function modifyMeme(Request $request, $id, ?User $user): Response
    {
        $requestBody = json_decode($request->getContent(), true) ?? [];
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
    public function likeMeme($id, ?User $user): Response
    {
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
    public function dislikeMeme($id, ?User $user): Response
    {
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
    public function reportMeme($id, Request $request, ?User $user): Response
    {
        $requestBody = json_decode($request->getContent(), true) ?? [];
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
    public function deleteMeme($id, ?User $user): Response
    {
        if (!$user) {
            throw new NotFoundHttpException("User not logged in");
        }
        $meme = $this->repo->find($id);
        if (!$meme) {
            throw new NotFoundHttpException("Meme not found");
        }
        if ($meme->getUser() !== $user) {
            throw new AccessDeniedHttpException("No permission to delete this meme");
        }
        $em = $this->doctrine->getManager();
        $em->remove($meme);
        $em->flush();
        return $this->json([
            'status' => 'success',
            'code' => 200
        ]);
    }
}