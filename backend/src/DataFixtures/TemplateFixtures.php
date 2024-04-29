<?php

namespace App\DataFixtures;

use App\Entity\Template;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TemplateFixtures extends Fixture
{
    /**
     * Loads Template fixtures into the database.
     *
     * @param ObjectManager $manager Provides access to database operations.
     */
    public function load(ObjectManager $manager)
    {
        /*
        for ($i = 0; $i < 10; $i++) {
            $template = new Template();
            $template->setTitle('Drake Hotline Bling ' . $i);
            $template->setURL('https://i.imgflip.com/30b1gx.jpg' . $i);
            $template->setImg('img data ' . $i);

            $manager->persist($template);
        }

        $manager->flush();
        */

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
}
