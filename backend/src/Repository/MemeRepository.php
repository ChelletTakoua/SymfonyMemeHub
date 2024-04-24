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

//    /**
//     * @return Meme[] Returns an array of Meme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Meme
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
