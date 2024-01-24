<?php

namespace Lib;

class Error
{
    public function __construct(
        protected int $code = 0,
        protected string $message = '',
    )
    {
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}