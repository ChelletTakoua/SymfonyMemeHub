<?php

namespace App\DataFixtures;

use App\Entity\Template;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TemplateFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * Loads Template fixtures into the database.
     *
     * @param ObjectManager $manager Provides access to database operations.
     */
    public function load(ObjectManager $manager)
    {
        $response = file_get_contents('https://api.imgflip.com/get_memes');

        $templates = json_decode($response, true);
        foreach ($templates['data']['memes'] as $templateData) {
            $template = new Template();
            $template->setTitle($templateData['name']);
            $template->setURL($templateData['url']);

            $manager->persist($template);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['template'];
    }
}
