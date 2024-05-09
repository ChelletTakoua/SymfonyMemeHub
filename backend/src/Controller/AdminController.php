<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


#[Route('/admin')]
class AdminController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private $repo;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repo = $this->doctrine->getRepository(User::class);
    }

    /** gets all the users and sends them in the response
     * @throws NotFoundHttpException
     */
    #[Route('/users', name: 'all_users')]
    public function getAllUsers(): JsonResponse
    {
        $users = $this->repo->findByRoleDESC("ROLE_USER");
        if (!$users) {
            throw new NotFoundHttpException("No users found");
        }

        return new JsonResponse(['users' => $users], Response::HTTP_OK);
    }

    /** gets all the admins and sends them in the response
     * @throws NotFoundHttpException
     */

    #[Route('/', name: 'admin_dashboard')]
    public function getAdminDashboard(): JsonResponse
    {

        $admins = $this->repo->findByRoleASC("ROLE_ADMIN");

        if ($admins) {
            return $this->json(['admins' => $admins], Response::HTTP_OK);
        }

        throw new NotFoundHttpException("No admins found");
    }

    /** takes in a user id and sends the user profile in the response
     * @param $id
     * @throws NotFoundHttpException
     */
    #[Route('/user/{id}', name: 'user_profile')]
    public function getUserProfile($id): Response
    {
        return $this->forward('App\Controller\UserController::getUserProfile',['id'=>$id]);
    }

    /** takes in a user id and changes the role of the user which is specified in the body of the request
     * @throws NotFoundHttpException
     * @throws BadRequestException
     */
    #[Route('/user/{id}/role', name: 'change_user_role', methods: ['POST'])]
    public function changeUserRole(Request $request, $id): Response
    {
        $data = $request->toArray();
        if (empty($data['roles'])) {
            return new JsonResponse(['message' => 'Role required'], Response::HTTP_BAD_REQUEST);
        }
        if ($data['roles'] != ["ROLE_ADMIN", "ROLE_USER"] && $data['roles'] != ["ROLE_USER"]) {
            throw new BadRequestException("Invalid role");
        }
        $roles = $data['roles'];
        $user = $this->repo->find($id);
        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }
        $user->setRoles($roles);
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json(["user" => $user]);
    }
}
