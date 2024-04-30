<?php

namespace App\DataFixtures;

use App\Entity\Meme;
use App\Entity\User;
use App\Entity\Report;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ReportFixtures extends Fixture implements FixtureGroupInterface
{

    /**
     * Loads Report fixtures into the database.
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
                $existingReport = $manager->getRepository(Report::class)->findOneBy([
                    'meme' => $meme,
                    'user' => $user,
                ]);

                if (!$existingReport) {
                    $report = new Report();
                    $report->setReason('Inappropriate content');
                    $report->setMeme($meme);
                    $report->setUser($user);

                    $manager->persist($report);
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
        return ['report'];
    }
}
