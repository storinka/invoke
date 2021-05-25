<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Exceptions\InvalidParamValueException;
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
            throw new InvalidParamValueException(
                $paramName,
                $this,
                $value,
                "min size \"{$this->minSize}\", got \"{$size}\""
            );
        }

        if ($this->maxSize && $size > $this->maxSize) {
            throw new InvalidParamValueException(
                $paramName,
                $this,
                $value,
                "max size \"{$this->maxSize}\", got \"{$size}\""
            );
        }

        foreach ($value as $i => $v) {
            $value[$i] = Typesystem::validateParam("{$paramName}[{$i}]", $this->itemType, $v);
        }

        return $value;
    }

    /**
     * @return mixed|string
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * @return mixed|null
     */
    public function getMinSize()
    {
        return $this->minSize;
    }

    /**
     * @return mixed|null
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }
}
