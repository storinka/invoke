<?php

namespace Invoke\NewMethod;

use Invoke\Pipe;

class ParameterInformationInformation implements ParameterInformationInterface
{
    public function __construct(protected readonly string      $name,
                                protected readonly string|Pipe $pipe,
                                protected readonly bool        $nullable,
                                protected readonly bool        $hasDefaultValue,
                                protected readonly mixed       $defaultValue,
                                protected readonly array       $validators,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPipe(): string|Pipe
    {
        return $this->pipe;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function isRequired(): bool
    {
        return !$this->hasDefaultValue();
    }

    public function getValidators(): array
    {
        return $this->validators;
    }
}