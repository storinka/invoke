<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Type;

class DefaultValueCustomType extends CustomType
{
    protected $defaultValue = null;

    public function __construct($type, $defaultValue = null)
    {
        $this->type = Type::Null($type);
        $this->defaultValue = $defaultValue;
    }

    public function validate(string $paramName, $value)
    {
        if ($value == null) {
            return $this->defaultValue;
        }

        return $value;
    }
}
