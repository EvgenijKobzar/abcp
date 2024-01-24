<?php

namespace Enum;

class CommunicationType
{
    const UNDEFINED = 0;
    const PHONE = 1;
    const EMAIL = 2;

    public const FIRST = 1;
    public const LAST = 2;

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