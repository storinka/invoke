<?php

namespace Invoke\Validations;

use Attribute;
use Invoke\Typesystem;
use Invoke\Validation;

#[Attribute]
class ArrayOf extends Validation
{
    public mixed $itemType;

    public function __construct(mixed $itemType)
    {
        $this->itemType = $itemType;
    }

    public function validate(string $paramName, $value): mixed
    {
        foreach ($value as $index => $item) {
            $value[$index] = Typesystem::validateParam("{$paramName}[$index]", $this->itemType, $item);
        }

        return $value;
    }
}