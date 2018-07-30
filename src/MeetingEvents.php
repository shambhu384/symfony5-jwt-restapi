<?php

declare(strict_types=1);

namespace App;

/**
 * Contains all events thrown in the FOSUserBundle.
 */

class MeetingEvents
{

    /**
     * The MEETING_REGISTERED event occurs when the meeting is initialized.
     *
     * @Event("App\Event\MeetingRegisteredEvent")
     */
    const MEETING_REGISTERED = 'meeting.registered';

    /**
     * The MEETING_REGISTERED event occurs when the meeting is initialized.
     *
     * @Event("App\Event\MeetingRegisteredEvent")
     */
    const MEETING_UNREGISTERED = 'meeting.registered';

    /**
     * The MEETING_REGISTERED event occurs when the meeting is initialized.
     *
     * @Event("App\Event\MeetingRegisteredEvent")
     */
    const MEETING_USER_REGISTERED = 'meeting.registered';

    /**
     * The MEETING_REGISTERED event occurs when the meeting is initialized.
     *
     * @Event("App\Event\MeetingRegisteredEvent")
     */
    const MEETING_USER_UNREGISTERED = 'meeting.registered';

    /**
     * The MEETING_REGISTERED event occurs when the meeting is initialized.
     *
     * @Event("App\Event\MeetingRegisteredEvent")
     */
    const USER_DEVICE_REGISTERED = 'meeting.registered';

    /**
     * The MEETING_REGISTERED event occurs when the meeting is initialized.
     *
     * @Event("App\Event\MeetingRegisteredEvent")
     */
    const USER_DEVICE_UNREGISTERED = 'meeting.registered';
}
