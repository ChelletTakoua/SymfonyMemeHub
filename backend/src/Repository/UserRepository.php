<?php

namespace App\Repository;

use App\Entity\User;
use App\Traits\SoftDeleteRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    use SoftDeleteRepositoryTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }
        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /** gets all the users the ordered by username ASC
     * @return User[] Returns an array of User objects
     */
    public function findByRoleASC($role): array
    {
        $qb = $this->getbaseQueryBuilder('u');
        $qb->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%')
            ->orderBy('u.username', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /** gets all the users the ordered by username DESC
     * @return User[] Returns an array of User objects
     */
    public function findByRoleDESC($role): array
    {
        $qb = $this->getbaseQueryBuilder('u');
        $qb->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%')
            ->orderBy('u.username', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**gets all the users that match the search term ordered by username ASC
     * @param $username
     * @return User[] Returns an array of User objects
     */
    public function findByUsernameASC($username): array
    {
        return $this->getbaseQueryBuilder('user')
            ->andWhere('user.username LIKE :username')
            ->setParameter('username', "%" . $username . "%")
            ->orderBy('user.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**gets all the users that match the search term ordered by username DESC
     * @param $username
     * @return User[] Returns an array of User objects
     */
    public function findByUsernameDESC($username): array
    {
        return $this->getbaseQueryBuilder('user')
            ->andWhere('user.username LIKE :username')
            ->setParameter('username', "%" . $username . "%")
            ->orderBy('user.username', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**gets all the users that match the email provided ordered by username ASC
     * @param $email
     * @return User[] Returns an array of User objects
     */
    public function findByEmailASC($email): array
    {
        return $this->getbaseQueryBuilder('user')
            ->andWhere('user.email LIKE :email')
            ->setParameter('email', "%" . $email . "%")
            ->orderBy('user.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**gets all the users that match the email provided ordered by username DESC
     * @param $email
     * @return User[] Returns an array of User objects
     */
    public function findByEmailDESC($email): array
    {
        return $this->getbaseQueryBuilder('user')
            ->andWhere('user.email LIKE :email')
            ->setParameter('email', "%" . $email . "%")
            ->orderBy('user.username', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**gets all the users ordered by their registration ASC
     * @return User[] Returns an array of User objects
     */
    public function findAllOrderedByRegisterDateASC(): array
    {
        return $this->findBy([], ['registrationDate' => 'ASC']);
    }

    /**gets all the users ordered by their registration DESC
     * @return User[] Returns an array of User objects
     */
    public function findAllOrderedByRegisterDateDESC(): array
    {
        return $this->findBy([], ['registrationDate' => 'DESC']);
    }


    /**gets all the users that are verified ordered by username ASC
     * @return User[] Returns an array of User objects
     */
    public function findAllVerifiedASC(): array
    {
        return $this->findBy(['verified' => true], ['username' => 'ASC']);
    }

    /**gets all the users that are verified ordered by username DESC
     * @return User[] Returns an array of User objects
     */
    public function findAllVerifiedDESC(): array
    {
        return $this->findBy(['verified' => true], ['username' => 'ASC']);
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->getbaseQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->getbaseQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
