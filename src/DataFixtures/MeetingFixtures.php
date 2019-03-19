<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use App\Entity\Meeting;
use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MeetingFixtures extends Fixture implements DependentFixtureInterface
{
    public const MEETING_MESSENGER_TALK_REFERENCE = 'meeting-messenger';

    public const MEETING_WORKFLOW_REFERENCE = 'meeting-workflow';

    public const MEETING_DEEP_DRIVE_REFERENCE = 'meeting-deep-drive';

    public const MEETING_CONTRIBUTION_REFERENCE = 'meeting-contribution';

    public function load(ObjectManager $manager)
    {
        $messangerMeeting = new Meeting();
        $messangerMeeting->setName("A talk on Symfony Messenger");
        $messangerMeeting->setDescription("Symfony's Messenger provides a message bus and some routing capabilities to send messages within your application and through transports such as message queues.Before using it, read the Messenger component docs to get familiar with its concepts.");
        $messengerTags = new Tag();
        $messengerTags->setName("Messenger");
        $messangerMeeting->setTag($messengerTags);
        $messangerMeeting->setDateTime(new \DateTime('now'));
        $messangerMeeting->setOrganiser($this->getReference(UserFixtures::USER_REFERENCE));
        $manager->persist($messangerMeeting);

        $workflowMeeting = new Meeting();
        $workflowMeeting->setName("A talk on Symfony Messenger");
        $workflowMeeting->setDescription("Symfony's Messenger provides a message bus and some routing capabilities to send messages within your application and through transports such as message queues. Before using it, read the Messenger component docs to get familiar with its concepts.");
        $workflowTags = new Tag();
        $workflowTags->setName("Messenger");
        $workflowMeeting->setTag($workflowTags);
        $workflowMeeting->setDateTime(new \DateTime('now'));
        $workflowMeeting->setOrganiser($this->getReference(UserFixtures::USER_REFERENCE));
        $manager->persist($workflowMeeting);

        $deepdriveMeeting = new Meeting();
        $deepdriveMeeting->setName("Tobias Nyholm \"Deep dive into Symfony 4 internals\"");
        $deepdriveMeeting->setDescription("Looks like you want to understand how Symfony2 works and how to extend it. That makes me very happy! This section is an in-depth explanation");
        $deepdriveTags = new Tag();
        $deepdriveTags->setName("Symfony 4 internals");
        $deepdriveMeeting->setTag($deepdriveTags);
        $deepdriveMeeting->setDateTime(new \DateTime('now'));
        $deepdriveMeeting->setOrganiser($this->getReference(UserFixtures::USER_REFERENCE));
        $manager->persist($deepdriveMeeting);


        $contributingMeeting = new Meeting();
        $contributingMeeting->setName("Contributing to Symfony");
        $contributingMeeting->setDescription("This part of the documentation tells you everything you want to know about how you can be involved with the Symfony project; from contributing documentation to fix bugs");
        $contributingTags = new Tag();
        $contributingTags->setName("Contributing");
        $contributingMeeting->setTag($contributingTags);
        $contributingMeeting->setDateTime(new \DateTime('now'));
        $contributingMeeting->setOrganiser($this->getReference(UserFixtures::USER_REFERENCE));
        $manager->persist($contributingMeeting);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
