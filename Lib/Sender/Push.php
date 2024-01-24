<?php

namespace Lib\Sender;

use NW\WebService\References\Operations\Notification\NotificationEvents;

class Push
{
    static public function send()
    {
        return NotificationManager::send(
            $resellerId,
            $client->id,
            NotificationEvents::CHANGE_RETURN_STATUS,
            (int)$data['differences']['to'],
            $templateData,
            $error
        );
    }
}