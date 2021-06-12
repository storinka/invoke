<?php

namespace Invoke\Typesystem\Exceptions;

class InvalidResultParamTypeException extends InvalidParamTypeException
{
    public function __construct(string $paramName, $paramType, $actualType)
    {
        parent::__construct($paramName, $paramType, $actualType, "INVALID_RESULT_PARAM_TYPE", 500);
    }
}
