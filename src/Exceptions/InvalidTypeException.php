<?php

namespace Invoke\Exceptions;

use Invoke\Type;
use Invoke\Utils\Utils;

class InvalidTypeException extends PipeException
{
    public readonly Type $expectedType;
    public readonly mixed $value;

    public function __construct(Type $expectedType, mixed $value)
    {
        $typeName = Utils::getPipeTypeName($expectedType);
        $valueTypeName = Utils::getValueTypeName($value);

        $this->expectedType = $expectedType;
        $this->value = $value;

        parent::__construct("Expected \"{$typeName}\", got \"{$valueTypeName}\".");
    }
}
