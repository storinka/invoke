<?php

namespace Invoke;

class InvalidFunctionException extends InvokeError
{
    public function __construct(string $functionName)
    {
        parent::__construct("INVALID_FUNCTION", "Invalid function \"$functionName\".", 400);
    }
}
