<?php

namespace App\Traits;

use App\Annotation\PreSoftDelete;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

trait SoftDeleteTrait
{

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }


    public function softDelete($em): void
    {
        $this->callPreSoftDelete($em);
        $this->deletedAt = new \DateTime();
        $em->persist($this);
        $em->flush();
    }

    private function callPreSoftDelete($em = null): void
    {
        $reflectionClass = new \ReflectionClass($this);
        foreach ($reflectionClass->getMethods() as $method) {
            if($this->hasAttribute($method, PreSoftDelete::class)){
                $methodName = $method->getName();
                $this->$methodName($em);
            }
        }
    }

    function hasAttribute(\ReflectionMethod $method, string $attributeName): bool {
        foreach ($method->getAttributes() as $attribute) {
            if ($attribute->getName() === $attributeName) {
                return true;
            }
        }
        return false;
    }

}