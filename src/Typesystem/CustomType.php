<?php

namespace Invoke\Typesystem;

abstract class CustomType
{
    /**
     * @var string|CustomType $baseType
     */
    protected $baseType;

    /**
     * @param string $paramName
     * @param $value
     *
     * @return mixed
     */
    public abstract function validate(string $paramName, $value);

    /**
     * @return string
     */
    public function toString(): string
    {
        return Typesystem::getTypeName($this->baseType);
    }

    /**
     * @return string|CustomType
     */
    public function getBaseType()
    {
        return $this->baseType;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
