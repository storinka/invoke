<?php

namespace Invoke\Typesystem;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

function getReflectionPropertyParam(ReflectionProperty $reflectionProperty, $object): array
{
    $name = $reflectionProperty->getName();

    $reflectionType = $reflectionProperty->getType();

    $type = $reflectionType;
    if ($reflectionType instanceof ReflectionNamedType) {
        $type = $reflectionType->getName();
    }

    $type = $type[0] === "?" ? substr($type, 1) : $type;
    if ($reflectionType->isBuiltin()) {
        $type = Typesystem::normalizeBuiltInType($type);
    }

    if (isset($object->{$name})) {
        $type = Type::Null($type, $object->{$name});
    } else if ($reflectionType->allowsNull()) {
        $type = Type::Null($type);
    }

    return [
        $name => $type,
    ];
}

abstract class PureType
{
    public function __construct($data)
    {
        $this->hydrate($data);
    }

    public static function params(): array
    {
        return [];
    }

    protected function hydrate($data): void
    {
        $reflectionClass = new ReflectionClass($this);

        $params = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $params = array_merge($params, getReflectionPropertyParam($reflectionProperty, $this));
        }
        $params = array_merge($params, static::params());

        $rendered = method_exists($this, "render") ? $this->render($data) : [];

        $result = Typesystem::validateParams($params, $data, $rendered);
        foreach ($result as $paramName => $paramValue) {
            $this->{$paramName} = $paramValue;
        }
    }

    public function toArray(): array
    {
        return (array)$this;
    }

    // --- builder functions ---

    public static function create($data): ?self
    {
        if (is_null($data)) {
            return null;
        }

        return new static($data);
    }

    public static function createArray($items): array
    {
        if (invoke_is_assoc($items)) {
            $items = array_values($items);
        }

        return array_map(fn($item) => new static($item), $items);
    }
}
