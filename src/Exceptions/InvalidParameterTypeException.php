<?php

namespace Invoke\Exceptions;

use Invoke\Type;
use Invoke\Utils\Utils;

/**
 * Value with invalid parameter type was provided.
 */
class InvalidParameterTypeException extends InvalidTypeException
{
    public string $path;

    public function __construct(string $path,
                                Type   $expectedType,
                                mixed  $valueTypeName)
    {
        parent::__construct($expectedType, $valueTypeName);

        $expectedTypeName = Utils::getPipeTypeName($expectedType);

        $this->message = "Invalid \"{$path}\" type: expected \"{$expectedTypeName}\", got \"{$valueTypeName}\".";
        $this->path = $path;
    }
}
