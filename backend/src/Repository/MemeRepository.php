<?php

namespace App\Repository;

use App\Entity\BlockedMeme;
use App\Entity\Like;
use App\Entity\Meme;
use App\Entity\User;
use App\Traits\SoftDeleteRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
//do not remove/overwride the find() method or it will break the code in the controller
/**
 * @extends ServiceEntityRepository<Meme>
 *
 * @method Meme|null find($id, $lockMode = null, $lockVersion = null)
 *
 */
class MemeRepository extends ServiceEntityRepository
{
    use SoftDeleteRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meme::class);

    }

    public function isLikedByUser(Meme $meme, User $user): bool
    {
        $likes = $this->getEntityManager()
            ->getRepository(Like::class)
            ->findBy(['meme' => $meme, 'user' => $user]);

        return !empty($likes);
    }

    /*public function findRandomMeme(): ?Meme
    {
        return $this->getMemeBaseQuery()
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }*/


    public function findAll(bool $includeBlocked = true): array
    {
        return $this->getMemeBaseQuery($includeBlocked)
            ->getQuery()
            ->getResult();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null,bool $includeBlocked = true): array
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


    private function getMemeBaseQuery(bool $includeBlocked = true){
        $queryBuilder = $this->getbaseQueryBuilder('m');

        if (!$includeBlocked) {
            $queryBuilder->leftJoin(BlockedMeme::class, 'bm', 'WITH', 'm.id = bm.meme')
                ->where('bm.meme IS NULL');
        }

        return $queryBuilder;
    }

    public function findOneBy(array $criteria, array $orderBy = null,bool $includeBlocked = true): ?Meme
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
    public function findPaginated(int $page = 1, int $pageSize = -1, string $order = 'desc', bool $includeBlocked = true): array
    {

        if ($page < 1) {
            throw new \InvalidArgumentException('Page number cannot be less than 1.');
        }

        if ($pageSize == -1) {
            return $this->findAll($includeBlocked);
        }

        $offset = ($page - 1) * $pageSize;

        return $this->findby([], ['creationDate' => $order], $pageSize, $offset, $includeBlocked);
    }

    public function memeIsBlocked(int $memeId): bool
    {
        return $this->createQueryBuilder('m')
                ->select('COUNT(bm.id)')
                ->leftJoin(BlockedMeme::class, 'bm', 'WITH', 'm.id = bm.meme')
                ->where('m.id = :memeId')
                ->setParameter('memeId', $memeId)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function getTotalPages(int $pageSize, bool $includeBlocked = true): int
    {
        $totalMemes = $this->getMemeBaseQuery($includeBlocked)
            ->select('COUNT(m.id)')
            ->getQuery()
            ->getSingleScalarResult();
        return ceil($totalMemes / $pageSize);
    }

    public function findMemesByUser(int $userId): array
    {
        return $this->findBy(['user' => $userId], ['creationDate' => 'DESC']);
    }

    public function findByASC(){
        return $this->createQueryBuilder('m')
            ->orderBy('m.creationDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findbyDESC(){
        return $this->createQueryBuilder('m')
            ->orderBy('m.creationDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBydate($date){
        return $this->createQueryBuilder('m')
            ->andWhere('m.creationDate = :val')
            ->setParameter('val', $date)
            ->getQuery()
            ->getResult()
        ; 
    }

    public function findByBlocked($blocked){
        $val=$blocked?"not null":"null";
        return $this->getbaseQueryBuilder('m')
            ->leftJoin(BlockedMeme::class, 'bm', 'WITH', 'm.id = bm.meme')
            ->where('bm.meme IS '.$val)
        ;
    }
    //finds the unblocked memes of a user 
    public function findByUser($idUser){
        return $this->findByBlocked(false)
                    ->where('m.user = '.$idUser)
                    ->getQuery()
                    ->getResult()
        ;
    }

}
