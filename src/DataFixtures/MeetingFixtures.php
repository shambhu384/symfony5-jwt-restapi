<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Meeting;

class MeetingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $meeting = new Meeting();
        $meeting->setName('Symfony apps');
        $meeting->setDescription('Symfony apps description');
        $meeting->setDateTime(new \DateTime('now'));
        // this reference returns the User object created in UserFixtures
        $meeting->setUser($this->getReference(UserFixtures::USER_REFERENCE));
        $manager->persist($meeting);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
