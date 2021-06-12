<?php

namespace Invoke\Typesystem\Exceptions;

class InvalidResultParamValueException extends InvalidParamValueException
{
    public function __construct(string $paramName, $paramType, $value, ?string $message = null)
    {
        parent::__construct($paramName, $paramType, $value, $message, "INVALID_RESULT_PARAM_VALUE", 500);
    }
}
