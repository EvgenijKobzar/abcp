<?php

namespace Lib\Handler;

use Entity\Client;
use Entity\Employee;
use Entity\EntityContractorFactory;
use Entity\Position;
use Entity\Seller;
use Enum\EntityType;
use Enum\NotificationType;
use Enum\StatusType;
use Lib\Communication\Manager;
use Lib\Env\Vars;
use Lib\Error;
use Lib\Result;

class HandlerReturn
{
    const CHANGE_RETURN_STATUS = 'changeReturnStatus';
    const NEW_RETURN_STATUS    = 'newReturnStatus';
    /**
     * @throws \ErrorException
     */
    public function process(): Result
    {
        $data = (array)$this->getRequest('data');

        $r = $this->verifySign($data);
        if($r->isSuccess() === false)
        {
            return $r;
        }

        $r = $this->isExpired($data);
        if($r->isSuccess() === false)
        {
            return $r;
        }

        $fields = static::internalize($data);

        $r = $this->validation($fields);
        if ($r->isSuccess())
        {
            $r = $this->loadEntities([
                'clientId' => $fields['clientId'],
                'expertId' => $fields['expertId'],
                'creatorId' => $fields['creatorId'],
                'resellerId' => $fields['resellerId']
            ]);
            if ($r->isSuccess())
            {
                $data = $r->getData();

                /** @var Client $client */
                $client = $data['client'];
                /** @var Employee $expert */
                $expert = $data['expert'];
                /** @var Employee $creator */
                $creator = $data['creator'];
                /** @var Seller $reseller */
                $reseller = $data['reseller'];

                if ($client->getSeller()->getId() !== $reseller->getId())
                {
                    $r->addError(new Error('Seller incorrect'));
                }
                else
                {
                    $positionId = 0;

                    if ($fields['notificationType'] === NotificationType::NEW)
                    {
                        $positionId = Position::add(['sellerId' => $reseller->getId()]);
                    }
                    else
                    {
                        if(
                            in_array($fields['differences']['to'], StatusType::getAll()) &&
                            in_array($fields['differences']['from'], StatusType::getAll())
                        )
                        {
                            //TODO: в запросе не приходит positionId ???
                            $positionId = Position::update($fields['positionId'], [
                                'sellerId' => $reseller->getId(),
                                'statusTo' => $fields['differences']['to'],
                                'statusFrom' => $fields['differences']['from'],
                            ])['id'];

                        }
                        else
                        {
                            $r->addError(new Error('Differences out of range'));
                        }
                    }

                    if($r->isSuccess())
                    {
                        if($positionId <= 0)
                        {
                            $r->addError(new Error('Position incorrect'));
                        }
                        else
                        {
                            $templateData = [
                                'COMPLAINT_ID'       => $fields['complaintId'], //TODO: нужно проверить существование такого id в репозитории
                                'COMPLAINT_NUMBER'   => $fields['complaintNumber'],
                                'CREATOR_ID'         => $creator->getId(),
                                'CREATOR_NAME'       => $creator->getFullName(),
                                'EXPERT_ID'          => $expert->getId(),
                                'EXPERT_NAME'        => $expert->getFullName(),
                                'CLIENT_ID'          => $client->getId(),
                                'CLIENT_NAME'        => $client->getFullName(),
                                'CONSUMPTION_ID'     => $fields['consumptionId'], //TODO: нужно проверить существование такого id в репозитории
                                'CONSUMPTION_NUMBER' => $fields['consumptionNumber'],
                                'AGREEMENT_NUMBER'   => $fields['agreementNumber'], //TODO: нужно проверить существование такого number в репозитории
                                'DATE'               => $fields['date'],
                                'DIFFERENCES'        => $fields['differences'],
                            ];

                            // TODO: рассмотреть возможность отправки через очередь - асинхронно
                            $r = $this->notificationSendDirectly(
                                $fields['notificationType'],
                                [
                                    'client' => $client,
                                    'reseller' => $reseller
                                ],
                                $fields['differences']['to'],
                                $templateData
                            );
                        }
                    }
                }
            }
        }

        return $r;
    }

    protected function notificationSendDirectly($type, $contractors, $statusId, $templateData): Result
    {
        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail'   => false,
            'notificationClientBySms'     => [
                'isSent'  => false,
                'message' => '',
            ],
        ];

        $r = $this->validateTemplateData($templateData);
        if ($r->isSuccess())
        {
            $client = $contractors['client'];
            $reseller = $contractors['reseller'];

            $r = (new Manager())->resolveEntityCommunicationData(EntityType::EMPLOYEE, $reseller, $templateData);
            if ($r->isSuccess())
            {
                $result['employees'] = $r->getData()['items'];
                $result['notificationEmployeeByEmail'] = true ; //MessagesClient::sendMessage($r->getData()['items'], $reseller->getId(), null, self::CHANGE_RETURN_STATUS, null);
            }

            // Шлём клиентское уведомление, только если произошла смена статуса
            if ($type === NotificationType::CHANGE)
            {
                if (in_array($statusId, StatusType::getAll()))
                {
                    $r = (new Manager())->resolveEntityCommunicationData(EntityType::CLIENT, $client, $templateData);

                    if ($r->isSuccess())
                    {
                        $result['notificationClientByEmail'] = true; //MessagesClient::sendMessage($r->getData(), $reseller->getId(), $client->getId(), self::CHANGE_RETURN_STATUS, $fields['differences']['to']);
                    }

                    if ($client->hasMobile())
                    {
                        $res = NotificationManager::send($reseller->getId(), $client->getId(), self::CHANGE_RETURN_STATUS, (int)$data['differences']['to'], $templateData, $error);
                        if ($res)
                        {
                            $result['notificationClientBySms']['isSent'] = true;
                        }
                        if (!empty($error))
                        {
                            $result['notificationClientBySms']['message'] = $error;
                        }
                    }
                }
            }

            $r->setData($result);
        }

        return $r;
    }

    protected function validateTemplateData(array $fields): Result
    {
        $r = new Result();

        foreach ($fields as $name => $value)
        {
            if (empty($value))
            {
                $r->addError(new Error("Template Data ({$name}) is empty!", 500));
            }
        }

        return $r;
    }

    /**
     * @throws \ErrorException
     */
    protected function loadEntities(array $fields): Result
    {
        $r = new Result();

        $loaded = false;

        $client = EntityContractorFactory::create(EntityType::CLIENT)::loadById($fields['clientId']);
        if($client)
        {
            $expert = EntityContractorFactory::create(EntityType::EMPLOYEE)::loadById($fields['expertId']);
            if($expert)
            {
                $creator = EntityContractorFactory::create(EntityType::EMPLOYEE)::loadById($fields['creatorId']);
                if($creator)
                {
                    $reseller = EntityContractorFactory::create(EntityType::SELLER)::loadById($fields['resellerId']);
                    if($reseller)
                    {
                        $loaded = true;
                    }
                }
            }
        }

        $loaded
            ? $r->setData([
                'client' => $client,
                'expert' => $expert,
                'creator' => $creator,
                'reseller' => $reseller,
            ])
            : $r->addError(new Error('Entity not found', 400));

        return $r;
    }

    /**
     * @param array $fields
     * @return Result
     */
    protected function validation(array $fields): Result
    {
        $r = $this->checkRequired($fields);
        if ($r->isSuccess())
        {
            if ($fields['notificationType'] === NotificationType::UNDEFINED)
            {
                $r->addError(new Error('params notificationType indefined'));
            }
        }
        return $r;
    }

    protected function checkRequired(array $fields): Result
    {
        $r = new Result();

        foreach ($this->requiredFields() as $name)
        {
            if (isset($fields[$name]) && (int)$fields[$name] > 0)
            {
                // do nothing
            }
            else
            {
                $r->addError(new Error('Empty parametr: '.$name, 100));
            }
        }
        return $r;
    }

    protected function requiredFields(): array
    {
        return [
            'clientId',
            'expertId',
            'creatorId',
            'resellerId',
            'notificationType',
            ];
    }

    static protected function internalize(array $fields): array
    {
        return [
                'clientId'=> isset($fields['clientId']) ? (int)$fields['clientId'] : 0,
                'expertId'=> isset($fields['expertId']) ? (int)$fields['expertId'] : 0,
                'creatorId'=> isset($fields['creatorId']) ? (int)$fields['creatorId'] : 0,
                'resellerId' => isset($fields['resellerId']) ? (int)$fields['resellerId'] : 0,
                'notificationType'=> isset($fields['notificationType'])
                                        && NotificationType::isDefined($fields['notificationType'])
                                ? (int)$fields['notificationType']
                                : NotificationType::UNDEFINED,
                'differences' => [
                    'from' => isset($fields['differences'])
                                && isset($fields['differences']['from'])
                                && StatusType::isDefined($fields['differences']['from'])
                        ? (int)$fields['differences']['from']
                        : StatusType::UNDEFINED,
                    'to' => isset($fields['differences'])
                                && isset($fields['differences']['to'])
                                && StatusType::isDefined($fields['differences']['to'])
                        ? (int)$fields['differences']['to']
                        : StatusType::UNDEFINED
                ],
                'complaintId' => isset($fields['complaintId']) ? (int)$fields['complaintId'] : 0,
                'complaintNumber' => isset($fields['complaintNumber']) ? (string)$fields['complaintNumber'] : '',
                'consumptionId' => isset($fields['consumptionId']) ? (int)$fields['consumptionId'] : 0,
                'consumptionNumber' => isset($fields['consumptionNumber']) ? (string)$fields['consumptionNumber'] : '',
                'agreementNumber' => isset($fields['agreementNumber']) ? (string)$fields['agreementNumber'] : '',
                'date' => isset($fields['date']) ? (string)$fields['date'] : '',
        ];
    }

    private function getRequest(string $name)
    {
        return $_REQUEST[$name];
    }

    private function verifySign($data): Result
    {
        $r = new Result();

        $fields = [
            'clientId',
            'expertId',
            'creatorId',
            'resellerId',
            'complaintId',
            'complaintNumber',
            'consumptionId',
            'consumptionNumber',
            'agreementNumber',
            'date',
            'expired',
        ];

        $sign_string = '';
        foreach ($fields as $field) {
            $sign_string .= (isset($data[$field]) ? $data[$field] : '') . ';';
        }

        $sign_string .= $this->getSecretKey();

        $sign = base64_encode(hash('sha256', $sign_string, true));

        $hash = isset($data['hash']) ? (string)$data['hash'] : null;

        if ( $sign !== $hash)
        {
            $r->addError(new Error('Hash invalid'), 500);
        }

        return $r;
    }

    private function isExpired($data): Result
    {
        $r = new Result();

        if (time() > (int)$data['expired'])
        {
            $r->addError(new Error('Token is expired', 500));
        }

        return $r;
    }

    /**
     * Метод возвращает secret для обработчика
     * файл настроек лежит выше DOCUMENT_ROOT либо в БД
     * @return string
     */
    private function getSecretKey(): string
    {
        return (string)Vars::getVar('handler')['secret_key'];
    }
}