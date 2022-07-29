<?php

namespace Invoke\Abstractions;

class NewMethodParameterDetails
{
    public function __construct(
        protected readonly string $name,
        protected readonly mixed  $pipe,
        protected readonly bool   $hasDefaultValue,
        protected readonly mixed  $defaultValue,
        protected readonly bool   $required,
        protected readonly bool   $nullable,
        protected readonly bool   $injectable,
        protected readonly array  $validators,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPipe(): mixed
    {
        return $this->pipe;
    }

    public function isHasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function isInjectable(): bool
    {
        return $this->injectable;
    }

    public function getValidators(): array
    {
        return $this->validators;
    }
}