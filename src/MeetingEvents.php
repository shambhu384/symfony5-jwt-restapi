<?php

namespace App;

/**
 * Contains all events thrown in the FOSUserBundle.
 */

class MeetingEvents {

	/**
     * The MEETING_REGISTERED event occurs when the meeting is initialized.
     *
     * @Event("App\Event\MeetingRegisteredEvent")
     */
    const MEETING_REGISTERED = 'meeting.registered';
}
