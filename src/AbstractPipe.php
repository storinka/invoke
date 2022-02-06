<?php

namespace Invoke;

use function invoke_get_class_name;

abstract class AbstractPipe implements Pipe
{
    public function getTypeName(): string
    {
        return invoke_get_class_name(static::class);
    }

    public function getValueTypeName(mixed $value): string
    {
        return Utils::getValueTypeName($value);
    }

    public function getUsedPipes(): array
    {
        return [];
    }
}