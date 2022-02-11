<?php

namespace Invoke\Exceptions;

use Invoke\Type;
use Invoke\Utils\Utils;

class ParamInvalidTypeException extends InvalidTypeException
{
    public string $path;

    public function __construct(string $path,
                                Type   $expectedType,
                                mixed  $value)
    {
        parent::__construct($expectedType, $value);

        $expectedTypeName = Utils::getPipeTypeName($expectedType);
        $valueType = Utils::getValueTypeName($value);

        $this->message = "Invalid \"{$path}\": expected \"{$expectedTypeName}\", got \"{$valueType}\".";
        $this->path = $path;
    }
}
