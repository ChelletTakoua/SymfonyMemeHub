<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
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
            $user->setPassword('password' . $i);
            $user->setRegDate(new \DateTime());
            $user->setRole('ROLE_USER');
            $user->setIsVerified(true);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
