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

    public function getSeller(): Seller
    {
        return new Seller();
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