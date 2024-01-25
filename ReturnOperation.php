<?php

namespace NW\WebService\References\Operations\Notification;

use Lib\Handler\HandlerReturn;

class TsReturnOperation extends ReferencesOperation
{
    public const TYPE_NEW    = 1;
    public const TYPE_CHANGE = 2;

    /**
     * @throws \Exception
     */
    // TODO: описать тим возвращаемого значения
    public function doOperation(): void // TODO: возвращаемое значение array
    {
        // refactoring
        return (new HandlerReturn())
            ->process();


        // TODO: нет проверки подписи всех параметров и проверки время истечения подписи
        $data = (array)$this->getRequest('data'); // TODO: в коде работаем !только! со списком параметром - по 'белому списку'
        $resellerId = $data['resellerId']; // TODO: необходима интернализация значения - (int)
        $notificationType = (int)$data['notificationType'];
        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail'   => false,
            'notificationClientBySms'     => [
                'isSent'  => false,
                'message' => '',
            ],
        ];

        if (empty((int)$resellerId)) {
            $result['notificationClientBySms']['message'] = 'Empty resellerId'; // TODO: ключ должен указывать на ошибку - errorMessage например
            return $result; // TODO: тип возвращаемого значения void
        }

        if (empty((int)$notificationType)) {
            throw new \Exception('Empty notificationType', 400);
        }

        $reseller = Seller::getById((int)$resellerId); // TODO: возвращается object. null никогда не вернется
        if ($reseller === null) {
            throw new \Exception('Seller not found!', 400);
        }

        $client = Contractor::getById((int)$data['clientId']);// TODO: возвращается object. null никогда не вернется
        if ($client === null || $client->type !== Contractor::TYPE_CUSTOMER || $client->Seller->id !== $resellerId) {
            throw new \Exception('сlient not found!', 400);
        }

        $cFullName = $client->getFullName();
        if (empty($client->getFullName())) {
            $cFullName = $client->name;
        }

        $cr = Employee::getById((int)$data['creatorId']);// TODO: возвращается object. null никогда не вернется
        if ($cr === null) {
            throw new \Exception('Creator not found!', 400);
        }

        $et = Employee::getById((int)$data['expertId']);// TODO: возвращается object. null никогда не вернется
        if ($et === null) {
            throw new \Exception('Expert not found!', 400);
        }

        $differences = '';
        if ($notificationType === self::TYPE_NEW) {
            $differences = __('NewPositionAdded', null, $resellerId);
        } elseif ($notificationType === self::TYPE_CHANGE && !empty($data['differences'])) {
            // TODO: нет параметра positionId для обновления объекта Position
            $differences = $this->__('PositionStatusHasChanged', [
                'FROM' => Status::getName((int)$data['differences']['from']), // TODO: @see 113. to - может быть пустым. требуется валидация
                'TO' => Status::getName((int)$data['differences']['to']), // TODO: @see 113. может быть пустым. требуется валидация
            ], $resellerId);
        }

        $templateData = [
            'COMPLAINT_ID'       => (int)$data['complaintId'],
            'COMPLAINT_NUMBER'   => (string)$data['complaintNumber'],
            'CREATOR_ID'         => (int)$data['creatorId'],
            'CREATOR_NAME'       => $cr->getFullName(),
            'EXPERT_ID'          => (int)$data['expertId'],
            'EXPERT_NAME'        => $et->getFullName(),
            'CLIENT_ID'          => (int)$data['clientId'],
            'CLIENT_NAME'        => $cFullName,
            'CONSUMPTION_ID'     => (int)$data['consumptionId'],
            'CONSUMPTION_NUMBER' => (string)$data['consumptionNumber'],
            'AGREEMENT_NUMBER'   => (string)$data['agreementNumber'],
            'DATE'               => (string)$data['date'],
            'DIFFERENCES'        => $differences,
        ];

        // Если хоть одна переменная для шаблона не задана, то не отправляем уведомления
        foreach ($templateData as $key => $tempData) {
            if (empty($tempData)) {
                throw new \Exception("Template Data ({$key}) is empty!", 500);
            }
        }

        $emailFrom = getResellerEmailFrom($resellerId);
        // Получаем email сотрудников из настроек
        $emails = getEmailsByPermit($resellerId, 'tsGoodsReturn'); // TODO: магическую строку необходимо вынести в константу
        if (!empty($emailFrom) && count($emails) > 0) {
            foreach ($emails as $email) { // TODO: в цикле вызывается MessagesClient::sendMessage, хотя видно что на вход пронимается список сообщений
                // TODO: требуется проанализировать - отправка осуществляется 'по месту' или через очередь, синхронно
                MessagesClient::sendMessage([ // TODO: нет возвращаемого значения
                    0 => [ // MessageTypes::EMAIL
                           'emailFrom' => $emailFrom,
                           'emailTo'   => $email,
                           'subject'   => $this->__('complaintEmployeeEmailSubject', $templateData, $resellerId),
                           'message'   => __('complaintEmployeeEmailBody', $templateData, $resellerId),
                    ],
                ], $resellerId, /* $client->id еще один параметр пропущен */,
                    //TODO: NotificationEvents::CHANGE_RETURN_STATUS - предполагаю должен коррелировать с self::TYPE_CHANGE, NotificationEvents::NEW_REURN_STATUS c self::TYPE_NEW
                    NotificationEvents::CHANGE_RETURN_STATUS); // TODO: в данном виде отсутствует возврат сообщения об ошибке (в виде ссылки &error)
                // TODO: для MessagesClient::sendMessage() нет возвращаемого значения, чтобы установить факт успешной отправки
                // TODO: установка значения осуществляется в цикле
                $result['notificationEmployeeByEmail'] = true; //TODO: структура успешного ответа notificationClientByEmail и notificationClientBySms.isSent отличается

            }
        }

        // Шлём клиентское уведомление, только если произошла смена статуса
        if ($notificationType === self::TYPE_CHANGE && !empty($data['differences']['to'])) { // TODO: получается что ожидается, что в поле to может придти неожиданный результат. значит надо выше проводить валидацию
            if (!empty($emailFrom) && !empty($client->email)) {
                // TODO: требуется проанализировать - отправка осуществляется 'по месту' или через очередь, синхронно
                MessagesClient::sendMessage([ // TODO: нет возвращаемого значения
                    0 => [ // MessageTypes::EMAIL
                           'emailFrom' => $emailFrom,
                           'emailTo'   => $client->email,
                           'subject'   => __('complaintClientEmailSubject', $templateData, $resellerId),
                           'message'   => __('complaintClientEmailBody', $templateData, $resellerId),
                    ],
                ], $resellerId, $client->id, NotificationEvents::CHANGE_RETURN_STATUS, (int)$data['differences']['to']); // TODO: в данном виде отсутствует возврат сообщения об ошибке
                // TODO: для MessagesClient::sendMessage() нет возвращаемого значения чтобы установить факт успешной отправки
                $result['notificationClientByEmail'] = true; //TODO: структура успешного ответа notificationClientByEmail и notificationClientBySms.isSent отличается
            }

            if (!empty($client->mobile)) {
                // TODO: требуется проанализировать - отправка осуществляется 'по месту' или через очередь, синхронно
                $res = NotificationManager::send($resellerId, $client->id, NotificationEvents::CHANGE_RETURN_STATUS, (int)$data['differences']['to'], $templateData, $error);
                if ($res) {
                    $result['notificationClientBySms']['isSent'] = true; //TODO: структура успешного ответа notificationClientByEmail и notificationClientBySms.isSent отличается
                }
                if (!empty($error)) {
                    $result['notificationClientBySms']['message'] = $error; // TODO: ключ должен указывать на ошибку - errorMessage например
                }
            }
        }

        return $result;
    }

    private function __($call, array $agr1, $agr2)
    {

    }

}
