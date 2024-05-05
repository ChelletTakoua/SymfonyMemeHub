<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\BadRequestException;


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
    #[Route('/admin/users', name: 'all_users')]
    public function getAllUsers(): JsonResponse
    {
        $users = $this->repo->getByRole("ROLE_USER");
        if (!$users) {
            throw new NotFoundHttpException("No users found");
        
        } else {
            return new JsonResponse(['users' => $users], Response::HTTP_OK);
        }
    }
     
    /** gets all the admins and sends them in the response
     * @throws NotFoundHttpException
     */

    #[Route('/admin', name: 'admin_dashboard')]
    public function getAdminDashboard(): JsonResponse
    {
        
        $admins = $this->repo->getByRole("ROLE_ADMIN");
            
            

        if ($admins) {
            return $this->json(['admins' => $admins], Response::HTTP_OK);
        } else {
            throw new NotFoundHttpException("No admins found");
        }
       }


       
    

    
     /** takes in a user id and sends the user profile in the response
     * @param $id
     * @throws NotFoundHttpException
     */
    #[Route('/admin/user/{id}', name: 'user_profile')]
    public function getUserProfile($id): JsonResponse
    {
        $user = $this->repo->find($id);
        if ($user) {
            return new JsonResponse(['user' => $user], Response::HTTP_OK);
        } else {
            throw new NotFoundHttpException("User not found");
        }
    }
    
     /** takes in a user id and changes the role of the user which is specified in the body of the request
     * @param $id
     * @throws NotFoundHttpException
     * @throws BadRequestException
     */
    #[Route('/admin/user/{id}/role', name: 'change_user_role',methods: ['POST'])]
    public function changeUserRole(Request $request ,$id): Response
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['roles'])) {
            return new JsonResponse(['message' => 'Role required'], Response::HTTP_BAD_REQUEST);
        }
        
        if ($data["roles"] != "ROLE_USER" && $data["roles"] != "ROLE_ADMIN") {
            return new JsonResponse(['message' => 'Invalid role '], Response::HTTP_BAD_REQUEST);
        }
        $role = $data['roles'];
        $user = $this->repo->find($id);
        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }
        $user->setRoles([$role]);
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json(["user" => $user]);
    }
    

}