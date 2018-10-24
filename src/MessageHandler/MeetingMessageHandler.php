<?php

namespace App\MessageHandler;

use App\Message\MeetingMessage;

class MeetingMessageHandler
{
    public function __invoke(MeetingMessage $message)
    {
        sleep(1);
        echo "We app runing:", date("r", time()). "\n";
        // Why do this work on request move in qeue
    }
}
