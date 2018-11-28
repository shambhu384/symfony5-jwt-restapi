<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\User;
use App\Entity\Meeting;
use App\Entity\Tag;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MeetingFixtures extends Fixture implements DependentFixtureInterface
{
    private $encoder;

    public const USER_REFERENCE = 'admin-user';

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $mettings = [
            [
                'title' => "A talk on Symfony Messenger",
                "desc" => "Symfony's Messenger provides a message bus and some routing capabilities to send messages within your application and through transports such as message queues.
                            Before using it, read the Messenger component docs to get familiar with its concepts.",
                "tags" => ["PHP", "Qeue", "Symfony"],
                "user"=> ["username" => "shambhu", "fullname" => "Shambhu Kumar", "email" => "shambhu384@gmail.com"]
            ],
            [
                "title" => "Workflow Component easy flow Management",
                "desc" => "The Workflow component provides tools for managing a workflow or finite state machine.",
                "tags" => ["Symfony Component"],
                "user" => ["username" => "scott", "fullname" => "Scott", "email" => "scott@webdev.org"]
            ],
            [
                "title" => "Tobias Nyholm \"Deep dive into Symfony 4 internals\"",
                "desc" => "Looks like you want to understand how Symfony2 works and how to extend it. That makes me very happy! This section is an in-depth explanation ",
                "tags" => ["Symfony2"],
                "user" => ["username" => "malay", "fullname" => "Malay Nayak", "email" => "malay@webdev.org"]
            ],
            [
                "title" => "Contributing to Symfony",
                "desc" => "This part of the documentation tells you everything you want to know about how you can be involved with the Symfony project; from contributing documentation to fix bugs",
                "tags" => ["OpenSource"],
                "user" => ["username" => "kevin", "fullname" => "kavin dagulus", "email" => "kavin@demo.org"]
            ],
            [
                "title" => "Code of Conduct",
                "desc" => "Looks like you want to understand how Symfony2 works and how to extend it. That makes me very happy! This section is an in-depth explanation ",
                "tags" => ["Symfony2"],
                "user" => ["username" => "ryan", "fullname" => "ryan weaver", "email" => "ryan@demo.org"]
            ],
            [
                "title" => "Dependency Injection and service containers ",
                "desc" => "The DependencyInjection component implements a PSR-11 compatible service container that allows you to standardize and centralize the way objects are constructed in your application.",
                "tags" => ["Symfony2"],
                "user" => ["username" => "user", "fullname" => "Test user", "email" => "test@webdev.org"]
            ],
            [
                "title" => "How important Logging",
                "desc" => "Symfony comes with a minimalist PSR-3 logger: Logger. In conformance with the twelve-factor app methodology, it sends messages starting from the WARNING level to stderr.",
                "tags" => ["Symfony2"],
                "user" => ["username" => "demo", "fullname" => "Demo App", "email" => "demo@webdev.org"]
            ]
        ];


        // this reference returns the User object created in UserFixtures
        $organiser = new User();
        $organiser->setUsername('admin');
        $organiser->setFullname('Organiser Team');
        $organiser->setEmail('learning@demo.org');
        $password = $this->encoder->encodePassword($organiser, 'pass_1234');
        $organiser->setPassword($password);
        $manager->persist($organiser);
        $manager->flush();


        foreach($mettings as $item) {
            $meeting = new Meeting();
            $meeting->setName($item["title"]);
            $meeting->setDescription($item["desc"]);
            $meeting->setDateTime(new \DateTime('now'));
            $meeting->setOrganiser($organiser->getId());

            $tag = new Tag();
            $tag->setName($item["tags"][0]);
            $manager->persist($tag);

            $meeting->setTag($tag);
            // this reference returns the User object created in UserFixtures
            $user = new User();
            $user->setUsername($item["user"]["username"]);
            $user->setFullname($item["user"]['fullname']);
            $user->setEmail($item["user"]["email"]);

            $password = $this->encoder->encodePassword($user, 'pass_1234');
            $user->setPassword($password);
            $manager->persist($user);

            $meeting->setUser($user);


            $manager->persist($meeting);

            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
