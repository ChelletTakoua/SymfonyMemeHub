<?php

namespace App\DataFixtures;

use App\Entity\Meme;
use App\Entity\TextBlock;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TextBlockFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Loads TextBlock fixtures into the database.
     *
     * @param ObjectManager $manager Provides access to database operations.
     */
    public function load(ObjectManager $manager)
    {
        $memes = $manager->getRepository(Meme::class)->findBy([], ['id' => 'ASC'], 2);

        if (count($memes) >= 2) {
            $textBlock1 = new TextBlock();
            $textBlock1->setText('Sample text 1');
            $textBlock1->setX(10);
            $textBlock1->setY(20);
            $textBlock1->setFontSize('12px');
            $textBlock1->setMeme($memes[0]);

            $manager->persist($textBlock1);

            $textBlock2 = new TextBlock();
            $textBlock2->setText('Sample text 2');
            $textBlock2->setX(30);
            $textBlock2->setY(40);
            $textBlock2->setFontSize('14px');
            $textBlock2->setMeme($memes[1]);

            $manager->persist($textBlock2);

            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            MemeFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['textblock'];
    }
}
