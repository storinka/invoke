<?php

namespace Invoke;

use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\TypeUtils;
use JsonSerializable;
use ReflectionClass;

class Data implements AsData, JsonSerializable
{
    /**
     * Creates an instance of the type.
     *
     * @param $data
     * @return static
     */
    public static function from($data): static
    {
        $type = new static();

        TypeUtils::hydrate($type, $data);

        return $type;
    }

    /**
     * Creates nullable instance of the type.
     *
     * @param $data
     * @return ?static
     */
    public static function nullable($data): ?static
    {
        if ($data === null) {
            return null;
        }

        return static::from($data);
    }

    public static function many(array $items): array
    {
        return array_map(fn($item) => static::from($item), $items);
    }

    public function toDataArray(): array
    {
        return array_merge(
            Invoke::$config["typesystem"]["typeNames"] ? ["@type" => invoke_get_class_name($this::class)] : [],
            get_object_vars($this),
        );
    }

    public function toArray(): array
    {
        return $this->toDataArray();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getDataParams(): array
    {
        $reflectionClass = new ReflectionClass($this);

        return ReflectionUtils::reflectionParamsOrPropsToInvoke($reflectionClass->getProperties());
    }
}