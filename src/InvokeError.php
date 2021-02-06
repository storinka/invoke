<?php

namespace Invoke;

use RuntimeException;

class InvokeError extends RuntimeException
{
    protected string $error;
    protected $data;

    public function __construct($error = "", $code = 500, $data = null)
    {
        parent::__construct($error, $code);

        $this->error = $error;
        $this->data = $data;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getData()
    {
        return $this->data;
    }
}
