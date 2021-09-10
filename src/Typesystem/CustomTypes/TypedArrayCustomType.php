<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\Exceptions\InvalidParamValueException;
use Invoke\Typesystem\GenericCustomType;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;

class TypedArrayCustomType extends GenericCustomType
{
    public ?int $minSize;
    public ?int $maxSize;

    public function __construct($itemType = Types::String,
                                ?int $minSize = null,
                                ?int $maxSize = null)
    {
        $this->baseType = Types::Array;
        $this->genericTypes = [$itemType];

        $this->minSize = $minSize;
        $this->maxSize = $maxSize;
    }

    public function validate(string $paramName, $value)
    {
        $size = sizeof(array_values($value));

        if ($this->minSize && $size < $this->minSize) {
            throw new InvalidParamValueException(
                $paramName,
                $this,
                $value,
                "Invalid \"{$paramName}\" size: min \"{$this->minSize}\", got \"{$size}\"."
            );
        }

        if ($this->maxSize && $size > $this->maxSize) {
            throw new InvalidParamValueException(
                $paramName,
                $this,
                $value,
                "Invalid \"{$paramName}\" size: max \"{$this->maxSize}\", got \"{$size}\"."
            );
        }

        $itemType = $this->genericTypes[0];


        foreach ($value as $i => $v) {
            $value[$i] = Typesystem::validateParam("{$paramName}[{$i}]", $itemType, $v);
        }

        return $value;
    }
}
