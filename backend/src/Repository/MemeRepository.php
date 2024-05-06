<?php

namespace App\Repository;

use App\Entity\Meme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Meme>
 *
 * @method Meme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Meme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Meme[]    findAll()
 * @method Meme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meme::class);
    }
    /**
     * Finds all memes in the repository, sorted by their creation date in ascending order.
     *
     * @return array An array of Meme entities, sorted by their creation date in ascending order.
     */
    public function findAllByDateASC(){
        return $this->createQueryBuilder('m')
            ->orderBy('m.creationDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    /**
     * Finds all memes in the repository, sorted by their creation date in descending order.
     *
     * @return array An array of Meme entities, sorted by their creation date in descending order.
     */
    public function findAllByDateDESC(){
        return $this->createQueryBuilder('m')
            ->orderBy('m.creationDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


//    public function findOneBySomeField($value): ?Meme
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
//  find like selon meme users asc username/meme created at selon date
//  
}
