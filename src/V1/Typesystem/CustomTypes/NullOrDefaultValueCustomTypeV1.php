<?php

namespace Invoke\V1\Typesystem\CustomTypes;

use Invoke\Typesystem\Type;
use Invoke\V1\Typesystem\CustomTypeV1;

class NullOrDefaultValueCustomTypeV1 extends CustomTypeV1
{
    protected $realBaseType = null;

    protected $defaultValue = null;

    public function __construct($baseType, $defaultValue = null)
    {
        $this->realBaseType = $baseType;
        $this->baseType = Type::Null($baseType);
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
