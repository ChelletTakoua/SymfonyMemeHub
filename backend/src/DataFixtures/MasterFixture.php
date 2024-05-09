<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class MasterFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadTemplates($manager);
        $this->loadMemes($manager);
        $this->loadTextBlocks($manager);
        $this->loadLikes($manager);
        $this->loadReports($manager);
        $this->loadBannedUsers($manager);
        $this->loadBlockedMemes($manager);
    }

    private function loadUsers(ObjectManager $manager)
    {
        $fixture = new UserFixtures();
        $fixture->load($manager);
    }

    private function loadTemplates(ObjectManager $manager)
    {
        $fixture = new TemplateFixtures();
        $fixture->load($manager);
    }

    private function loadMemes(ObjectManager $manager)
    {
        $fixture = new MemeFixtures();
        $fixture->load($manager);
    }

    private function loadTextBlocks(ObjectManager $manager)
    {
        $fixture = new TextBlockFixtures();
        $fixture->load($manager);
    }

    private function loadLikes(ObjectManager $manager)
    {
        $fixture = new LikeFixtures();
        $fixture->load($manager);
    }

    private function loadReports(ObjectManager $manager)
    {
        $fixture = new ReportFixtures();
        $fixture->load($manager);
    }

    private function loadBannedUsers(ObjectManager $manager)
    {
        $fixture = new BannedUserFixtures();
        $fixture->load($manager);
    }

    private function loadBlockedMemes(ObjectManager $manager)
    {
        $fixture = new BlockedMemeFixtures();
        $fixture->load($manager);
    }

    public static function getGroups(): array
    {
        return ['master'];
    }
}
