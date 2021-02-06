<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\InvokeError;
use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Type;
use Invoke\Typesystem\Typesystem;

class TypedArrayCustomType extends CustomType
{
    protected $type = Type::Array;

    protected $itemType;
    protected $minSize;
    protected $maxSize;

    public function __construct($itemType = Type::String, $minSize = null, $maxSize = null)
    {
        $this->itemType = $itemType;
        $this->minSize = $minSize;
        $this->maxSize = $maxSize;

        $this->stringRepresentation = "Array<" . Typesystem::getTypeName($itemType) . ">";
    }

    public function validate(string $paramName, $value)
    {
        $size = sizeof(array_values($value));

        if ($this->minSize && $size < $this->minSize) {
            throw new InvokeError("INVALID_PARAM_ARRAY_MIN_SIZE_VALUE");
        }

        if ($this->maxSize && $size > $this->maxSize) {
            throw new InvokeError("INVALID_PARAM_ARRAY_MAX_SIZE_VALUE");
        }

        foreach ($value as $i => $v) {
            $value[$i] = Typesystem::validateParam("{$paramName}[{$i}]", $this->itemType, $v);
        }

        return $value;
    }
}
