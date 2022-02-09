<?php

namespace Invoke\Validators;

use Attribute;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Support\HasDynamicName;
use Invoke\Support\HasUsedTypes;
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
        $value = ArrayType::getInstance()->pass($value);

        foreach ($value as $index => $item) {
            try {
                $value[$index] = $this->itemPipe->pass($item);
            } catch (InvalidTypeException|ValidationFailedException) {
                // ignore
            }
        }

        return $value;
    }

    public function getUsedTypes(): array
    {
        return [$this->itemPipe];
    }

    public static function getName(): string
    {
        return "array";
    }

    public function getDynamicName(): string
    {
        $arrayTypeName = ArrayType::getName();
        $itemPipeName = Utils::getPipeTypeName($this->itemPipe);

        return "{$arrayTypeName}<{$itemPipeName}>";
    }
}