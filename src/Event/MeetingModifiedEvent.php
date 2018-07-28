<?php

declare(strict_types=1);


namespace App\Event;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\Meeting;

class MeetingModifiedEvent extends Event
{
    protected $meeting;

    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * Get meeting.
     *
     * @return Meeting
     */
    public function getMeeting(): Meeting
    {
        return $this->meeting;
    }

    /**
     * Set meeting.
     *
     * @param Meeting $meeting
     */
    public function setMeeting(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }
}
