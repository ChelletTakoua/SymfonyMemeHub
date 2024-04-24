<?php

namespace App\Repository;

use App\Entity\BlockedMeme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlockedMeme>
 *
 * @method BlockedMeme|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockedMeme|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockedMeme[]    findAll()
 * @method BlockedMeme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockedMemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockedMeme::class);
    }

//    /**
//     * @return BlockedMeme[] Returns an array of BlockedMeme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BlockedMeme
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
