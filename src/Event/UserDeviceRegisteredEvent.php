<?php

declare(strict_types=1);


namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\Meeting;

class UserDeviceRegisteredEvent extends Event
{
    protected $meeting;

    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Get meeting.
     *
     * @return meeting.
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * Set meeting.
     *
     * @param meeting the value to set.
     */
    public function setMeeting(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }
}
