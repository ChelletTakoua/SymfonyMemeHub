<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\BannedUser;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class BannedUserFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Loads BannedUser fixtures into the database.
     *
     * @param ObjectManager $manager Provides access to database operations.
     */
    public function load(ObjectManager $manager)
    {
        $userRepo = $manager->getRepository(User::class);
        $users = $userRepo->findBy([], [], 5);
        foreach ($users as $user) {

            if ($user->getUsername() === 'admin') continue;
            $bannedUser = new BannedUser();
            $bannedUser->setUser($user);
            $bannedUser->setBanDuration(rand(1, 30));
            $bannedUser->setReason('Test reason');
            $bannedUser->setAdmin($userRepo->findOneBy(['username' => 'admin']));
            $manager->persist($bannedUser);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['bannedUser'];
    }
}
