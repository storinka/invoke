<?php

namespace Invoke\Validations;

use Attribute;
use Invoke\Exceptions\InvalidParamValueException;
use Invoke\Validation;

#[Attribute]
class Length extends Validation
{
    public ?int $min;
    public ?int $max;

    public function __construct(?int $min = null, ?int $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function validate(string $paramName, $value): mixed
    {
        if (function_exists("mb_strlen")) {
            $length = mb_strlen($value);
        } else {
            $length = strlen($value);
        }

        if (!is_null($this->min)) {
            if ($length < $this->min) {
                throw new InvalidParamValueException(
                    $paramName,
                    $this,
                    $value,
                    "Invalid \"{$paramName}\" length: min \"{$this->min}\", got \"{$length}\"."
                );
            }
        }

        if (!is_null($this->max)) {
            if ($length > $this->max) {
                throw new InvalidParamValueException(
                    $paramName,
                    $this,
                    $value,
                    "Invalid \"{$paramName}\" length: max \"{$this->max}\", got \"{$length}\"."
                );
            }
        }

        return $value;
    }

    public function __toString()
    {
        return "...";
    }
}