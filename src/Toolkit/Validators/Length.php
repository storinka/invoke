<?php

namespace Invoke\Toolkit\Validators;

use Attribute;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Stop;
use Invoke\Validator;

/**
 * String length validator.
 */
#[Attribute]
class Length implements Validator
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
        if ($value instanceof Stop) {
            return $value;
        }

        $length = mb_strlen($value);

        if (!is_null($this->min)) {
            if ($length < $this->min) {
                throw new ValidationFailedException(
                    "min length {$this->min}, got {$length}"
                );
            }
        }

        if (!is_null($this->max)) {
            if ($length > $this->max) {
                throw new ValidationFailedException(
                    "max length {$this->max}, got {$length}"
                );
            }
        }

        return $value;
    }
}
