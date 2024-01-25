<?php

namespace Entity;

use Enum\EntityType;


class Client extends Contractor
{
    protected string $phone;
    static public function getType(): int
    {
        return EntityType::CLIENT;
    }

    /**
     * @throws \ErrorException
     */
    public function getSeller(): Seller
    {
        return EntityContractorFactory::create(EntityType::SELLER)::loadById(2);
    }

    public function hasMobile(): bool
    {
        return strlen($this->phone) > 0;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}