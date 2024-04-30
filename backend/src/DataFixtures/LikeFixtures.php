<?php

namespace App\DataFixtures;

use App\Entity\Like;
use App\Entity\Meme;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class LikeFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Loads Like fixtures into the database.
     *
     * @param ObjectManager $manager Provides access to database operations.
     */
    public function load(ObjectManager $manager)
    {
        $test = false;
        $memes = $manager->getRepository(Meme::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            if ($test) {
                break;
            }
            foreach ($memes as $meme) {
                $existingLike = $manager->getRepository(Like::class)->findOneBy([
                    'meme' => $meme,
                    'user' => $user,
                ]);

                if (!$existingLike) {
                    $like = new Like();
                    $like->setMeme($meme);
                    $like->setUser($user);

                    $manager->persist($like);

                    $test = true;
                    break;
                }
            }
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
        return ['like'];
    }
}
