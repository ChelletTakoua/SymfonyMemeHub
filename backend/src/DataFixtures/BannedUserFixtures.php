<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\BannedUser;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BannedUserFixtures extends Fixture
{
    /**
     * Loads BannedUser fixtures into the database.
     *
     * @param ObjectManager $manager Provides access to database operations.
     */
    public function load(ObjectManager $manager)
    {
        $userRepo = $manager->getRepository(User::class);
        $bannedUserRepo = $manager->getRepository(BannedUser::class);

        $bannedUsers = $bannedUserRepo->findAll();
        $bannedUserIds = array_map(function ($bannedUser) {
            return $bannedUser->getUser()->getId();
        }, $bannedUsers);

        $queryBuilder = $userRepo->createQueryBuilder('u');
        if (!empty($bannedUserIds)) {
            $queryBuilder->where($queryBuilder->expr()->notIn('u.id', $bannedUserIds));
        }
        $queryBuilder->setMaxResults(4);
        $users = $queryBuilder->getQuery()->getResult();

        foreach ($users as $user) {
            $bannedUser = new BannedUser();
            $bannedUser->setUser($user);
            $bannedUser->setBanDate(new \DateTime());
            $bannedUser->setBanDuration(rand(1, 30));
            $bannedUser->setReason('Test reason');

            $manager->persist($bannedUser);
        }

        $manager->flush();
    }
}
