<?php

namespace Invoke\Exceptions;

class InvalidFunctionException extends InvokeException
{
    public function __construct(string $functionName)
    {
        parent::__construct("INVALID_FUNCTION", "Invalid function \"$functionName\".", 400);
    }
}
