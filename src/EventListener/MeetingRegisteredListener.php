<?php

declare(strict_types=1);


namespace App\EventListener;

use Symfony\Component\EventDispatcher\Event;

class MeetingRegisteredListener
{

    /**
     * @param Event $event
     * @return void
     */
    public function onMeetingRegistered(Event $event): void
    {
        // Send notification to devices
    }
}
