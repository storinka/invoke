<?php

namespace Invoke\Typesystem\Utils;

use Invoke\Typesystem\Typesystem;
use ReflectionClass;

class TypeUtils
{
    public static function validate($type, $data): array
    {
        $reflectionClass = new ReflectionClass($type);

        $params = ReflectionUtils::inspectInvokeTypeReflectionClassParams($reflectionClass, $type);

        // map values through "render" method
        $rendered = [];
        if (method_exists($type, "render")) {
            $rendered = $type->render($data);
        }

        // validate params
        $result = Typesystem::validateParams($params, $data, $rendered);

        return $result;
    }

    public static function hydrate($type, $data): array
    {
        $result = static::validate($type, $data);

        // fill class properties with the data
        foreach ($result as $paramName => $paramValue) {
            $type->{$paramName} = $paramValue;
        }

        return $result;
    }
}
