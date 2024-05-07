<?php

namespace App\Traits;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

trait SoftDeleteTrait
{

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deletedAt=null;

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function softDelete($em)
    {
        $this->deletedAt = new \DateTime();
        $em->persist($this);
        $em->flush();
    }
}