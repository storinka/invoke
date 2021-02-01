<?php

namespace Invoke;

use RuntimeException;

class InvalidFunctionException extends RuntimeException
{
    public function __construct($functionName)
    {
        parent::__construct("Invalid function \"{$functionName}\"", 400);
    }
}
