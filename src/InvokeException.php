<?php

namespace Invoke;

use RuntimeException;

class InvokeException extends RuntimeException
{
    protected string $error;
    protected ?array $data;

    public function __construct(
        string $error,
        string $message,
        int $code = 500,
        ?array $data = null
    )
    {
        parent::__construct($message, $code);

        $this->error = $error;
        $this->data = $data;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
