<?php

namespace App\MessageHandler;

use App\Message\MeetingMessage;

class MeetingMessageHandler
{
    public function __invoke(MeetingMessage $message)
    {
        // sleep(3);
        // Why do this work on request move in qeue
    }
}
