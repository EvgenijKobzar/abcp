<?php

namespace Lib;

class Result
{
    protected array $errors = [];
    protected array $data = [];

    public function isSuccess(): bool
    {
        return count($this->errors) <= 0;
    }

    /**
     * @param Error $error
     * @return void
     */
    public function addError(Error $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorsMessage(): array
    {
        $result = [];
        /** @var Error $error */
        foreach ($this->getErrors() as $error)
        {
            $result[] = $error->getMessage();
        }

        return $result;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}