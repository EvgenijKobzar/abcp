<?php

namespace Entity;

use Enum\EntityType;

class Contractor
{
    static public function getType(): int
    {
        return EntityType::UNDEFINED;
    }

    public function __construct(
        protected int $id = 0,
        protected string $name = '',
        protected string $email = '',
        protected int $type = EntityType::UNDEFINED,
    )
    {
    }

    static public function loadById(int $id): null|static
    {
        $row = static::getRepository()->getList([
            'select' => ['id', 'name', 'typeId','email'],
            'filter' => ['id' => $id, 'typeId' => static::getType()]
        ]);

        if (empty($row['id']))
        {
              return null;
        }
        else
        {
            return new static(
                $row['id'],
                $row['name'],
                $row['email'],
                $row['typeId'],
            );
        }
    }

    /**
     * @return __anonymous@982
     */
    static protected function getRepository()
    {
         return new class {
            public function getList($params): array
            {
                $name = EntityType::resolveName($params['filter']['typeId']).'_'.$params['filter']['id'];

                return [
                    'id' => $params['filter']['id'],
                    'name' => $name,
                    'typeId' => $params['filter']['typeId'],
                    'email' => $name.'@mail.com'
                ];
            }
        };
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}