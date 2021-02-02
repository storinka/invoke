<?php

namespace Invoke;

class InvalidFunctionException extends InvokeError
{
    public function __construct($functionName)
    {
        parent::__construct("INVALID_FUNCTION", 400);
    }
}
