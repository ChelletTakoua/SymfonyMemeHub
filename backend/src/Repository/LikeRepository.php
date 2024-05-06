<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 *
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }
    /**
     * Finds and returns all likes for a specific meme.
     *
     * @param int $MemeId The id of the meme to find likes for.
     * @return Like[] Returns an array of Like objects that match the criteria.
     *
    */
    public function findLikesByMeme($MemeId){
        return $this->createQueryBuilder('l')
            ->andWhere('l.meme = :memeId')
            ->setParameter('memeId', $MemeId)
            ->getQuery()
            ->getResult()
        ;
    }
    /**
     * Finds the likes associated with a specific user .
     *
     * @param int $UserId The ID of the user.
     * @return User[] The array of likes associated with the user.
     */
    public function findLikesByUser($UserId){
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :userId')
            ->setParameter('userId', $UserId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLikeByUserAndMeme($UserId, $MemeId)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :userId')
            ->andWhere('l.meme = :memeId')
            ->setParameter('userId', $UserId)
            ->setParameter('memeId', $MemeId)
            ->getQuery()
            ->getOneOrNullResult();

    }

//    public function findOneBySomeField($value): ?Like
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
