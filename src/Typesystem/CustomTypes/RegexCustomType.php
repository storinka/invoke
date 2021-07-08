<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Exceptions\InvalidParamValueException;
use Invoke\Typesystem\Types;

class RegexCustomType extends CustomType
{
    protected string $pattern;

    public function __construct(string $pattern)
    {
        $this->baseType = Types::String;

        $this->pattern = $pattern;
    }

    public function validate(string $paramName, $value)
    {
        if (!preg_match($this->pattern, $value)) {
            throw new InvalidParamValueException(
                $paramName,
                $this,
                $value,
                "does not match pattern \"{$this->pattern}\""
            );
        }

        return $value;
    }
}
