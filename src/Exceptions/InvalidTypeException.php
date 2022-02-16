<?php

namespace Invoke\Exceptions;

use Invoke\Type;
use Invoke\Utils\Utils;

/**
 * Value with invalid type was provided.
 */
class InvalidTypeException extends PipeException
{
    public readonly Type $expectedType;
    public readonly mixed $valueTypeName;

    public function __construct(Type $expectedType, string $valueTypeName)
    {
        $typeName = Utils::getPipeTypeName($expectedType);

        $this->expectedType = $expectedType;
        $this->valueTypeName = $valueTypeName;

        parent::__construct("Expected \"{$typeName}\", got \"{$valueTypeName}\".");
    }
}
