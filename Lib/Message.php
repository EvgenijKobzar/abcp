<?php

namespace Lib;
use Enum\MessageType;
use Lib\Sender\Email;
use Lib\Sender\Push;

class Message
{
    public function sendDirectly(array $messageFields)
    {
        $type = $messageFields['TYPE'];
        if ($type === MessageType::PUSH)
        {
            $r = Push::send();
            if (!$r)
            {
                $r = Email::send();
            }
        }
        else
        {
            $r = Email::send();
        }

        return $r;
    }
}