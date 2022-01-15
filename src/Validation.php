<?php

namespace Invoke;

abstract class Validation
{
    public abstract function validate(string $paramName, $value): mixed;

    public static function getName(): string
    {
        return invoke_get_class_name(static::class);
    }

    public function getDescription(): string
    {
        return "A validation.";
    }
}