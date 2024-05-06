<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Loads User fixtures into the database.
     *
     * @param ObjectManager $manager Provides access to database operations.
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername('User ' . $i);
            $user->setEmail('user' . $i . '@example.com');
            $user->setPassword(password_hash('user', PASSWORD_DEFAULT));
            $manager->persist($user);
        }
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@gmail.com');
        $user->setPassword(password_hash('admin', PASSWORD_DEFAULT));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setVerified(true);
        $manager->persist($user);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}
