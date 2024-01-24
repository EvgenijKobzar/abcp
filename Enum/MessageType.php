<?php

namespace Enum;

class MessageType
{
    public const UNDEFINED = 0;
    public const PUSH = 1;
    public const EMAIL = 2;

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