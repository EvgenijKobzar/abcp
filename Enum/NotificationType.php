<?php

namespace Enum;

class NotificationType
{
    public const UNDEFINED = 0;
    public const NEW = 1;
    public const CHANGE = 2;

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