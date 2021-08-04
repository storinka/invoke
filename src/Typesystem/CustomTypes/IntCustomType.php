<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Exceptions\InvalidParamValueException;
use Invoke\Typesystem\Types;

class IntCustomType extends CustomType
{
    protected $minValue;
    protected $maxValue;

    public function __construct($minValue = null, $maxValue = null)
    {
        $this->baseType = Types::Int;

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

    public function toString(): string
    {
        $min = !is_null($this->minValue) ? "min: {$this->minValue}" : null;
        $max = !is_null($this->maxValue) ? "max: {$this->maxValue}" : null;

        $params = "";

        if ($min) {
            $params .= $min;
        }

        if ($max) {
            if ($min) {
                $params .= ", ";
            }

            $params .= $max;
        }

        $string = "int";

        if ($params) {
            $string .= ": ($params)";
        }

        return $string;
    }
}
