<?php

namespace Invoke\Typesystem;

abstract class CustomType
{
    /**
     * Basically a native type. But also can be @CustomType.
     *
     * @var string|CustomType $type
     */
    protected $type;

    /**
     * String representation of the type. If not set, then from $type will be used.
     *
     * @var null|string $stringRepresentation
     */
    protected $stringRepresentation;

    /**
     * @param string $paramName
     * @param $value
     *
     * @return mixed
     */
    public abstract function validate(string $paramName, $value);

    /**
     * @return string|CustomType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getStringRepresentation(): string
    {
        if (is_null($this->stringRepresentation)) {
            return Typesystem::getTypeName($this->type);
        }

        return $this->stringRepresentation;
    }
}
