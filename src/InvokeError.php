<?php

namespace Invoke;

use RuntimeException;
use Throwable;

class InvokeError extends RuntimeException
{
    protected string $error;

    public function __construct($error = "", $code = 500, Throwable $previous = null)
    {
        parent::__construct($error, $code, $previous);

        $this->error = $error;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
