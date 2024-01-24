<?php

namespace Enum;

class StatusType
{
    public const UNDEFINED = -1;
    public const COMPLETED = 0;
    public const PENDING = 1;
    public const REJECTED = 2;

    public const COMPLETED_NAME = 'Completed';
    public const PENDING_NAME = 'Pending';
    public const REJECTED_NAME = 'Rejected';


    public const FIRST = 0;
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

    public static function resolveId($name): int
    {
        if(!is_string($name))
        {
            return self::UNDEFINED;
        }

        if($name === self::COMPLETED_NAME)
        {
            return self::COMPLETED;
        }
        elseif($name === self::PENDING_NAME)
        {
            return self::PENDING;
        }
        elseif($name === self::REJECTED_NAME)
        {
            return self::REJECTED;
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

        if($id === self::COMPLETED)
        {
            return self::COMPLETED_NAME;
        }
        elseif($id === self::PENDING)
        {
            return self::PENDING_NAME;
        }
        elseif($id === self::REJECTED)
        {
            return self::REJECTED_NAME;
        }
        return '';
    }

    static public function getAll(): array
    {
        return [
            self::PENDING,
            self::REJECTED,
            self::REJECTED,
        ];
    }
}