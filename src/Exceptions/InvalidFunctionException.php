<?php

namespace Invoke\Exceptions;

class InvalidFunctionException extends InvokeException
{
    public function __construct(string $functionName)
    {
        parent::__construct("Invalid function \"$functionName\".", 400);
    }
}
