<?php

namespace Entity;

use Enum\EntityType;

class Seller extends Contractor
{
    const PERMISSION_GOODS_RETURN = 'tsGoodsReturn';

    static public function getType(): int
    {
        return EntityType::SELLER;
    }

    public function getCollectionEmployeesByFilter($filter): array
    {
        $filter['typeId'] = static::getType();

        return static::getRepository()->getList([
            'select' => ['id', 'name', 'typeId','email'],
            'filter' => $filter
        ])->fetchAll();
    }
}