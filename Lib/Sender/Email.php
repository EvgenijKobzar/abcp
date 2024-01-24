<?php

namespace Lib\Sender;

use NW\WebService\References\Operations\Notification\NotificationEvents;

class Email
{
    static public function send()
    {
        return MessagesClient::sendMessage(
            [
                0 => [ // MessageTypes::EMAIL
                    'emailFrom' => $emailFrom,
                    'emailTo'   => $client->email,
                    'subject'   => __('complaintClientEmailSubject', $templateData, $resellerId),
                    'message'   => __('complaintClientEmailBody', $templateData, $resellerId),
                ],
            ],
            $resellerId,
            $client->id,
            NotificationEvents::CHANGE_RETURN_STATUS,
            (int)$data['differences']['to']
        );
    }
}