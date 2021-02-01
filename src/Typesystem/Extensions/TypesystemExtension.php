<?php

namespace Invoke\Typesystem\Extensions;

abstract class TypesystemExtension
{
    public abstract function apply($paramName, $paramType, $value, $valueType);
}
