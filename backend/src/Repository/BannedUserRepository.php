<?php

namespace App\Repository;

use App\Entity\BannedUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BannedUser>
 *
 * @method BannedUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method BannedUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method BannedUser[]    findAll()
 * @method BannedUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannedUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BannedUser::class);
    }

    /**gets all the banned users ordered by username ASC
     * @return BannedUser[] Returns an array of BannedUser objects
     */
    public function findAllOrdered(): array
    {

        return $this->createQueryBuilder('bannedUser')
            ->leftJoin('bannedUser.user', 'user')
            ->addSelect('user')
            ->orderBy('user.username', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**gets all the banned users that match the search term ordered by username ASC
     * @param $username
     * @return BannedUser[] Returns an array of BannedUser objects
     */
    public function findByTerm($username): array
    {
        return $this->createQueryBuilder('bannedUser')
            ->leftJoin('bannedUser.user', 'user')
            ->addSelect('user')
            ->andWhere('user.username LIKE :username 
                                   OR user.email LIKE :username')
            ->setParameter('username', "%".$username."%")
            ->orderBy('user.username', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**gets all the banned users that match the email provided ordered by username ASC
     * @param $email
     * @return BannedUser[] Returns an array of BannedUser objects
     */
    public function findByEmail($email): array
    {
        return $this->createQueryBuilder('bannedUser')
            ->leftJoin('bannedUser.user', 'user')
            ->addSelect('user')
            ->andWhere('user.email LIKE :email')
            ->setParameter('email', "%".$email."%")
            ->orderBy('user.username', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /** gets all the banned users that match the username provided ordered by username ASC
     * @param $username
     * @return BannedUser[] Returns an array of BannedUser objects
     */
    public function findByUsername($username): array
    {
        return $this->createQueryBuilder('bannedUser')
            ->leftJoin('bannedUser.user', 'user')
            ->addSelect('user')
            ->andWhere('user.username LIKE :username')
            ->setParameter('username', "%".$username."%")
            ->orderBy('user.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
