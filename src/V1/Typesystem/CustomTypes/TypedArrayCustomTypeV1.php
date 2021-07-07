<?php

namespace Invoke\V1\Typesystem\CustomTypes;

use Invoke\V1\Typesystem\Exceptions\InvalidParamValueExceptionV1;
use Invoke\V1\Typesystem\GenericCustomTypeV1;
use Invoke\V1\Typesystem\Types;
use Invoke\V1\Typesystem\TypesystemV1;

class TypedArrayCustomTypeV1 extends GenericCustomTypeV1
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
            throw new InvalidParamValueExceptionV1(
                $paramName,
                $this,
                $value,
                "min size \"{$this->minSize}\", got \"{$size}\""
            );
        }

        if ($this->maxSize && $size > $this->maxSize) {
            throw new InvalidParamValueExceptionV1(
                $paramName,
                $this,
                $value,
                "max size \"{$this->maxSize}\", got \"{$size}\""
            );
        }

        $itemType = $this->genericTypes[0];


        foreach ($value as $i => $v) {
            $value[$i] = TypesystemV1::validateParam("{$paramName}[{$i}]", $itemType, $v);
        }

        return $value;
    }
}
