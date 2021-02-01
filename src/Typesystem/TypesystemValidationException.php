<?php

namespace Invoke\Typesystem;

use RuntimeException;

class TypesystemValidationException extends RuntimeException
{
    public function __construct($paramName, $paramType, $actualType)
    {
        $paramType = Typesystem::getTypeName($paramType);
        $actualType = Typesystem::getTypeName($actualType);

        parent::__construct("Invalid \"{$paramName}\" type: expected \"{$paramType}\", got \"{$actualType}\".", 500);
    }
}
