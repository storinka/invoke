<?php

namespace Invoke;

use Attribute;
use Invoke\Pipes\AnyPipe;
use JsonSerializable;

#[Attribute]
abstract class Validator extends AbstractPipe implements JsonSerializable
{
    public function getValidationData(): array
    {
        return [];
    }

    public function jsonSerialize(): array
    {
        return [];
    }

    public function getValidatorName(): string
    {
        return invoke_get_class_name(static::class);
    }

    public function toType(): Pipe
    {
        return AnyPipe::getInstance();
    }
}