<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Exceptions\InvalidParamValueException;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;

class InCustomType extends CustomType
{
    protected array $values;

    public function __construct(array $values, $type = Types::String)
    {
        $this->baseType = $type;

        $this->values = $values;
    }

    public function validate(string $paramName, $value)
    {
        if (!in_array($value, $this->values)) {
            $itemsString = implode(", ", $this->values);

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
        $baseType = Typesystem::getTypeAsString($this->baseType);
        $items = implode(", ", $this->values);

        return "$baseType(values: $items)";
    }
}
