<?php

namespace Invoke\V1\Typesystem;

use Invoke\V1\Typesystem\Utils\ReflectionUtils;
use ReflectionClass;
use RuntimeException;

abstract class TypeV1 implements InvokeType
{
    /**
     * Validated params during hydration.
     *
     * @var array
     */
    private array $_validatedParams = [];

    /**
     * Create an instance of the type and hydrate it with the data.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->hydrate($data);
    }

    /**
     * Type params.
     *
     * @return array
     */
    public static function params(): array
    {
        return [];
    }

    /**
     * Validate the data and hydrate the type.
     *
     * @param $data
     */
    protected function hydrate($data): void
    {
        $reflectionClass = new ReflectionClass($this);

        $params = ReflectionUtils::inspectInvokeTypeReflectionClassParams($reflectionClass, $this);

        // do values mapping through "render" method
        $rendered = [];
        if (method_exists($this, "render")) {
            $rendered = $this->render($data);
        }

        // validate params
        $result = TypesystemV1::validateParams($params, $data, $rendered);

        // fill class properties with the data
        foreach ($result as $paramName => $paramValue) {
            $this->{$paramName} = $paramValue;
        }

        $this->_validatedParams = $result;
    }

    /**
     * Get validated params..
     *
     * @return array
     */
    public function getValidatedParams(): array
    {
        return $this->_validatedParams;
    }

    // factory methods

    /**
     * Creates an instance of the type. If data is null, then null is returned.
     *
     * @param $data
     * @return static|null
     */
    public static function create($data): ?self
    {
        if (is_null($data)) {
            return null;
        }

        return new static($data);
    }

    // util methods

    public function jsonSerialize(): array
    {
        return $this->getValidatedParams();
    }

    public function offsetGet($offset)
    {
        return $this->getValidatedParams()[$offset];
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->getValidatedParams());
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException("Unsupported!");
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException("Unsupported!");
    }
}
