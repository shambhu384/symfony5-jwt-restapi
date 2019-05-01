<?php

declare(strict_types=1);


namespace App\EventSubscriber;

use App\Event\MeetingRegisteredEvent;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MeetingRegisteredSubscriber implements EventSubscriberInterface
{
    use LoggerAwareTrait;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
            'meeting.registered' => array(
                array('processMeeting', 10),
                array('logMeeting', 0),
                array('notifyMeeting', -10),
            )
        );
    }

    public function processMeeting(MeetingRegisteredEvent $event)
    {
        $this->logger->info(sprintf('Metting initilise : %s', $event->getMeeting()->getName()));
    }

    public function logMeeting(MeetingRegisteredEvent $event)
    {
        $this->logger->info(sprintf('Meeting created: %s', $event->getMeeting()->getName()));
    }

    public function notifyMeeting(MeetingRegisteredEvent $event)
    {
        $this->logger->info(sprintf('Notify users about new : %s', $event->getMeeting()->getName()));
    }
}
