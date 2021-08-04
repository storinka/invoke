<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Exceptions\InvalidParamValueException;
use Invoke\Typesystem\Types;

class InCustomType extends CustomType
{
    protected $items;

    public function __construct($items, $type = Types::String)
    {
        $this->baseType = $type;

        $this->items = $items;
    }

    public function validate(string $paramName, $value)
    {
        if (!in_array($value, $this->items)) {
            $itemsString = implode(", ", $this->items);

            throw new InvalidParamValueException(
                $paramName,
                $this,
                $value,
                "Invalid param \"{$paramName}\" value: expected [$itemsString], got \"{$value}\"."
            );
        }

        return $value;
    }

    public function toString(): string
    {
        return implode(", ", $this->items);
    }
}
