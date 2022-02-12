<?php

namespace Invoke\Toolkit\Validators;

use Attribute;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Meta\HasDynamicName;
use Invoke\Piping;
use Invoke\Schema\HasUsedTypes;
use Invoke\Stop;
use Invoke\Type;
use Invoke\Types\ArrayType;
use Invoke\Utils\Utils;
use Invoke\Validator;

/**
 * Array item type validator.
 *
 * Can be used as type to validate nested arrays.
 */
#[Attribute]
class ArrayOf implements Validator, Type, HasDynamicName, HasUsedTypes
{
    public Type $itemPipe;

    public function __construct(Type|string|array $itemPipe)
    {
        $this->itemPipe = Utils::toType($itemPipe);
    }

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        $value = Piping::run(ArrayType::class, $value);

        foreach ($value as $index => $item) {
            try {
                $value[$index] = Piping::run($this->itemPipe, $item);
            } catch (InvalidTypeException | ValidationFailedException) {
                // ignore
            }
        }

        return $value;
    }

    public function invoke_getUsedTypes(): array
    {
        return [$this->itemPipe];
    }

    public static function invoke_getTypeName(): string
    {
        return "array";
    }

    public function invoke_getDynamicName(): string
    {
        $arrayTypeName = ArrayType::invoke_getTypeName();
        $itemPipeName = Utils::getPipeTypeName($this->itemPipe);

        return "{$arrayTypeName}<{$itemPipeName}>";
    }
}
