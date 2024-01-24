<?php

namespace Enum;

class EntityType
{
    public const UNDEFINED = 0;
    public const CLIENT = 1;
    public const SELLER = 2;
    public const EMPLOYEE = 3;

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
}