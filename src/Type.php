<?php

namespace Invoke;

class Type
{
    protected string $paramName;
    protected mixed $value;

    public function __construct(string $paramName,
                                mixed  $value)
    {
        $this->paramName = $paramName;
        $this->value = $value;
    }
}