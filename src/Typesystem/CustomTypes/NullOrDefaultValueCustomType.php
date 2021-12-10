<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Types;

class NullOrDefaultValueCustomType extends CustomType
{
    protected $realBaseType = null;

    protected $defaultValue = null;

    public function __construct($baseType, $defaultValue = null)
    {
        $this->realBaseType = $baseType;
        $this->baseType = Types::Null($baseType);
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
