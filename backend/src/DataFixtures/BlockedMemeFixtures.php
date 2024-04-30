<?php

namespace App\DataFixtures;

use App\Entity\Meme;
use App\Entity\User;
use App\Entity\BlockedMeme;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BlockedMemeFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Loads BlockedMeme fixtures into the database.
     *
     * @param ObjectManager $manager Provides access to database operations.
     */
    public function load(ObjectManager $manager)
    {
        $memeRepo = $manager->getRepository(Meme::class);
        $blockedMemeRepo = $manager->getRepository(BlockedMeme::class);

        $blockedMemes = $blockedMemeRepo->findAll();
        $blockedMemeIds = array_map(function ($blockedMeme) {
            return $blockedMeme->getMeme()->getId();
        }, $blockedMemes);

        $queryBuilder = $memeRepo->createQueryBuilder('m');
        if (!empty($blockedMemeIds)) {
            $queryBuilder->where($queryBuilder->expr()->notIn('m.id', $blockedMemeIds));
        }
        $queryBuilder->setMaxResults(4);

        $memes = $queryBuilder->getQuery()->getResult();

        foreach ($memes as $meme) {
            $blockedMeme = new BlockedMeme();
            $blockedMeme->setMeme($meme);
            $admin = $manager->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);
            $blockedMeme->setAdmin($admin);

            $manager->persist($blockedMeme);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MemeFixtures::class,
            UserFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['blockedMeme'];
    }
}
