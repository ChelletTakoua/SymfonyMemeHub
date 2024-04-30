<?php

namespace App\EventSubscriber;

use App\Entity\Like;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class LikeSubscriber implements EventSubscriber
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Like) {
            $meme = $entity->getMeme();
            $meme->setNumLikes($meme->getNumLikes() + 1);
        }
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist' => 'prePersist',
        ];
    }
}
