<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\InvokeError;
use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Type;

class RegexCustomType extends CustomType
{
    protected $type = Type::String;

    protected $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function validate(string $paramName, $value)
    {
        if (!preg_match($this->pattern, $value)) {
            throw new InvokeError("VALUE_IS_NOT_MATCHED");
        }

        return $value;
    }
}
