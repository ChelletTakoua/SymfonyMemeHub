<?php

namespace App\Repository;

use App\Entity\Meme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
//do not remove/overwride the find() method or it will break the code in the controller
/**
 * @extends ServiceEntityRepository<Meme>
 *
 * @method Meme|null find($id, $lockMode = null, $lockVersion = null)
 */
class MemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meme::class);
    }
    

    /*public function findRandomMeme(): ?Meme
    {
        return $this->getMemeBaseQuery()
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }*/


    public function findAll(bool $includeBlocked = false): array
    {
        return $this->getMemeBaseQuery($includeBlocked)
            ->getQuery()
            ->getResult();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null,bool $includeBlocked = false): array
    {
        $queryBuilder = $this->getMemeBaseQuery($includeBlocked);

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("m.$field = :$field")
                ->setParameter($field, $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $sort => $order) {
                $queryBuilder->addOrderBy("m.$sort", $order);
            }
        }

        if ($limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder->getQuery()->getResult();
    }


    private function getMemeBaseQuery(bool $includeBlocked = false){
        $queryBuilder = $this->createQueryBuilder('m');

        if (!$includeBlocked) {
            $queryBuilder->leftJoin('App\Entity\BlockedMeme', 'bm', 'WITH', 'm.id = bm.meme')
                ->where('bm.meme IS NULL');
        }

        return $queryBuilder;
    }

    public function findOneBy(array $criteria, array $orderBy = null,bool $includeBlocked = false): ?Meme
    {
        $queryBuilder = $this->getMemeBaseQuery($includeBlocked);

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("m.$field = :$field")
                ->setParameter($field, $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $sort => $order) {
                $queryBuilder->addOrderBy("m.$sort", $order);
            }
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     *  This method returns memes paginated.
     *
     * @param int $page The page number to return.
     * @param int $pageSize The number of memes to return per page. If -1 is passed, all memes will be returned.
     * @param bool $includeBlocked If true, blocked memes will be included in the results.
     * @return Meme[]
     *
     */
    public function findPaginated(int $page = 1, int $pageSize = -1, bool $includeBlocked = false): array
    {
        if ($page < 1) {
            throw new \InvalidArgumentException('Page number cannot be less than 1.');
        }

        if ($pageSize == -1) {
            return $this->findAll($includeBlocked);
        }

        $offset = ($page - 1) * $pageSize;

        return $this->getMemeBaseQuery($includeBlocked)
            ->setFirstResult($offset)
            ->setMaxResults($pageSize)
            ->getQuery()
            ->getResult();
    }

    public function memeIsBlocked(int $memeId): bool
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(bm.id)')
            ->leftJoin('App\Entity\BlockedMeme', 'bm', 'WITH', 'm.id = bm.meme')
            ->where('m.id = :memeId')
            ->setParameter('memeId', $memeId)
            ->getQuery()
            ->getSingleScalarResult() > 0;
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
//  find like selon meme users asc username/meme created at selon date
//  
}
