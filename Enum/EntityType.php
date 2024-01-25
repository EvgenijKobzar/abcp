<?php

namespace Enum;

class EntityType
{
    public const UNDEFINED = 0;
    public const CLIENT = 1;
    public const SELLER = 2;
    public const EMPLOYEE = 3;

    public const CLIENT_NAME = 'Client';
    public const SELLER_NAME = 'Seller';
    public const EMPLOYEE_NAME = 'Employee';

    public const FIRST = 1;
    public const LAST = 3;

    public static function isDefined($id): bool
    {
        if(!is_numeric($id))
        {
            return false;
        }

        $id = (int)$id;

        return $id >= self::FIRST && $id <= self::LAST;
    }

    public static function resolveId($name): int
    {
        if(!is_string($name))
        {
            return self::UNDEFINED;
        }

        if($name === self::CLIENT_NAME)
        {
            return self::CLIENT;
        }
        elseif($name === self::SELLER_NAME)
        {
            return self::SELLER;
        }
        elseif($name === self::EMPLOYEE_NAME)
        {
            return self::EMPLOYEE;
        }

        return self::UNDEFINED;
    }

    public static function resolveName($id): string
    {
        if(!is_numeric($id))
        {
            return '';
        }

        $id = (int)$id;
        if($id < 0)
        {
            return '';
        }

        if($id === self::CLIENT)
        {
            return self::CLIENT_NAME;
        }
        elseif($id === self::SELLER)
        {
            return self::SELLER_NAME;
        }
        elseif($id === self::EMPLOYEE)
        {
            return self::EMPLOYEE_NAME;
        }
        return '';
    }
}