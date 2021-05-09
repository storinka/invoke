<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Exceptions\InvalidParamValueException;
use Invoke\Typesystem\Type;

class IntCustomType extends CustomType
{
    protected $type = Type::Int;

    protected $minValue;
    protected $maxValue;

    public function __construct($minValue = null, $maxValue = null)
    {
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

    public function validate(string $paramName, $value)
    {
        if (!is_null($this->minValue)) {
            if ($value < $this->minValue) {
                throw new InvalidParamValueException(
                    $paramName,
                    $this,
                    $value,
                    "min \"{$this->minValue}\", got \"{$value}\""
                );
            }
        }

        if (!is_null($this->maxValue)) {
            if ($value > $this->maxValue) {
                throw new InvalidParamValueException(
                    $paramName,
                    $this,
                    $value,
                    "max \"{$this->maxValue}\", got \"{$value}\""
                );
            }
        }

        return $value;
    }
}
