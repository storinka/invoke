<?php

namespace Invoke\Validations;

use Attribute;
use Invoke\Exceptions\ParamValidationFailedException;
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

    public function pass(mixed $value): mixed
    {
        $length = mb_strlen($value);

        if (!is_null($this->min)) {
            if ($length < $this->min) {
                throw new ParamValidationFailedException(
                    "{$this->parentPipe->getTypeName()}::{$this->paramName}",
                    $this,
                    $value
                );
            }
        }

        if (!is_null($this->max)) {
            if ($length > $this->max) {
                throw new ParamValidationFailedException(
                    "{$this->parentPipe->getTypeName()}::{$this->paramName}",
                    $this,
                    $value
                );
            }
        }

        return $value;
    }

    public function getTypeName(): string
    {
        if (isset($this->min) && isset($this->max)) {
            return "length(min: {$this->min}, max: {$this->max})";
        }

        if (isset($this->min)) {
            return "length(min: {$this->min})";
        }

        if (isset($this->max)) {
            return "length(max: {$this->max})";
        }

        return "length()";
    }

    public function getValueTypeName(mixed $value): string
    {
        $length = mb_strlen($value);

        return "length($length)";
    }
}