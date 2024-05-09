<?php

namespace App\Traits;

use Doctrine\ORM\QueryBuilder;

trait SoftDeleteRepositoryTrait
{
    public function getbaseQueryBuilder($alias): QueryBuilder
    {
        return $this->createQueryBuilder($alias)
            ->andWhere($alias . '.deletedAt IS NULL');
    }

}
