<?php

namespace Lib\Communication;
use Entity\Client;
use Entity\Contractor;
use Entity\Seller;
use Enum\EntityType;
use Lib\Error;
use Lib\Result;

class Manager
{
    public function resolveEntityCommunicationData($typeId, Contractor $entity, $templateData): Result
    {
        $r = new Result();

        if ($typeId === EntityType::EMPLOYEE)
        {
            /** @var Seller $entity */
            $emailFrom = $this->getCommunicationData($entity); //TODO: нейминг метода оставил прежним
            $emails = $this->getResellerEmailFrom($entity); //TODO: нейминг метода оставил прежним

            if (empty($emailFrom))
            {
                $r->addError(new Error('Reseller email is empty'));
            }

            if (count($emails) <= 0)
            {
                $r->addError(new Error('Employees emails not found'));
            }

            $items = [];
            if ($r->isSuccess())
            {
                foreach ($emails as $email)
                {
                    $items[] =
                        [ // MessageTypes::EMAIL
                            'emailFrom' => $emailFrom,
                            'emailTo'   => $email,
                            'subject'   => $this->__('complaintEmployeeEmailSubject', $templateData, $entity->getId()),
                            'message'   => __('complaintEmployeeEmailBody', $templateData, $entity->getId()),
                        ];
                }

                $r->setData(['items' => $items]);
            }
        }
        elseif ($typeId === EntityType::CLIENT)
        {
            /** @var Client $entity */
            $reseller = $entity->getSeller();
            $emailFrom = $this->getCommunicationData($reseller); //TODO: нейминг метода оставил прежним

            if (empty($emailFrom))
            {
                $r->addError(new Error('Reseller email is empty'));
            }

            if (empty($entity->getEmail()))
            {
                $r->addError(new Error('Client email is empty'));
            }

            if ($r->isSuccess())
            {
                $items[] = [
                    // MessageTypes::EMAIL
                    'emailFrom' => $emailFrom,
                    'emailTo'   => $entity->getEmail(),
                    'subject'   => __('complaintClientEmailSubject', $templateData, $reseller->getId()),
                    'message'   => __('complaintClientEmailBody', $templateData, $reseller->getId()),
                ];

                $r->setData(['items' => $items]);
            }
        }

        return $r;
    }

    protected function getResellerEmailFrom(Seller $reseller): array
    {
        $items = $reseller->getCollectionEmployeesByFilter(['permit' => Seller::PERMISSION_GOODS_RETURN]);
        $emails = [];
        foreach ($items as $item)
        {
            $emails[] = $item['email'];
        }
        return $emails;
    }

    public function getCommunicationData(Contractor $entity): string
    {
        return $entity->getEmail();
    }

    protected function __($call, $arg1, $arg2)
    {
        // TODO: Implement __() method.
    }
}