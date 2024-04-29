<?php

namespace App\DataFixtures;

use App\Entity\Meme;
use App\Entity\User;
use App\Entity\Template;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class MemeFixtures extends Fixture
{
    /**
     * Loads Meme fixtures into the database.
     *
     * @param ObjectManager $manager Provides access to database operations.
     */
    public function load(ObjectManager $manager)
    {
        $userRepo = $manager->getRepository(User::class);
        $users = $userRepo->findAll();

        $templateRepo = $manager->getRepository(Template::class);
        $templates = $templateRepo->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                $meme = new Meme();
                $meme->setTitle('Example Meme ' . $i);
                $meme->setCreationDate(new \DateTime());
                $meme->setNumLikes(0);
                $meme->setUser($user);

                $meme->setTemplate($templates[array_rand($templates)]);

                $manager->persist($meme);
            }
        }

        $manager->flush();
    }
}
