<?php

namespace Invoke\Validations;

use Invoke\Validation;

class NullOrDefault extends Validation
{
    public mixed $default;

    public function __construct(mixed $default = null)
    {
        $this->default = $default;
    }

    public function validate(string $paramName, $value): mixed
    {
        if (is_null($value)) {
            return $this->default;
        }

        return $value;
    }

    public function __toString()
    {
        return "Nullable value.";
    }
}