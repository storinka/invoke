<?php

namespace Invoke\Exceptions;

class InvalidParamValueException extends TypesystemValidationException
{
    public function __construct(string $message,
                                int    $code = 500)
    {
        parent::__construct(
            "INVALID_PARAM_VALUE",
            $message,
            $code,
        );
    }
}
