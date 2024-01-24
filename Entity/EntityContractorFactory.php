<?php

namespace Entity;

use Enum\EntityType;

class EntityContractorFactory
{
    /**
     * @param $typeId
     * @return Employee|Client|Seller
     * @throws \ErrorException
     */
    static public function create($typeId): Employee|Client|Seller
    {
        if(!is_int($typeId))
        {
            $typeId = (int)$typeId;
        }

        if (EntityType::isDefined($typeId) === false)
        {
            throw new \ErrorException('Is not defined');
        }
        elseif ($typeId === EntityType::CLIENT)
        {
            return new Client();
        }
        elseif ($typeId === EntityType::SELLER)
        {
            return new Seller();
        }
        elseif ($typeId === EntityType::EMPLOYEE)
        {
            return new Employee();
        }
        else
        {
            throw new \ErrorException('Entity type: '.$typeId.' is not supported in current context');
        }
    }
}