<?php

namespace Entity;

use Enum\EntityType;

class Employee extends Contractor
{

    static public function getType(): int
    {
        return EntityType::EMPLOYEE;
    }
}