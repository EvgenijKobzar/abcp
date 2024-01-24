<?php

namespace Entity;

use Enum\StatusType;

class Position
{
    public function __construct(
        protected int $id,
        protected int $sellerId = 0,
        protected string $statusFrom = StatusType::UNDEFINED,
        protected int $statusTo = StatusType::UNDEFINED,
    )
    {
    }

    static public function loadById(int $id): null|static
    {
        $row = static::getRepository()->getList([
            'select' => ['id', 'sellerId', 'statusFrom', 'statusTo'],
            'filter' => ['id' => $id]
        ]);

        if (empty($row['id']))
        {
            return null;
        }
        else
        {
            return new static(
                $row['id'],
                $row['sellerId'],
                $row['statusFrom'],
                $row['statusTo'],
            );
        }
    }

    static protected function getRepository(): void
    {
        // TODO: Implement getRepository() method.
    }

    public function getLastStatus()
    {
        return $this->statusTo;
    }

    public function getFieldsValues(): array
    {
        return [
            'id' => $this->id,
            'sellerId' => $this->sellerId,
            'statusTo' => $this->statusTo,
            'statusFrom' => $this->statusFrom,
        ];
    }

    static public function add(array $fields):int
    {
        return 1;
    }

    static public function update($id, $fields): array
    {
        return (new self($id, ...$fields))->getFieldsValues();
    }
}