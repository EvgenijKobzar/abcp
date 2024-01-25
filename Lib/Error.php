<?php

namespace Lib;

class Error
{
    public function __construct(
        protected string $message = '',
        protected int $code = 0,
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