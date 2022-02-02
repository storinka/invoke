<?php

namespace Invoke\Utils;

use Closure;
use Ds\Set;
use Invoke\AsData;
use Invoke\Typesystem;

class TypeUtils
{
    public static function validate(AsData $type, $data): array
    {
        $params = $type->getDataParams();

        // map values through "render" method
        $rendered = [];
        if (method_exists($type, "render")) {
            $rendered = $type->render($data);
        }

        // validate params
        return Typesystem::validateParams(
            $params,
            $data,
            $rendered,
        );
    }

    public static function hydrate(AsData $type, $data): array
    {
        $result = static::validate($type, $data);

        // fill class properties with the data
        foreach ($result as $paramName => $paramValue) {
            $type->{$paramName} = $paramValue;
        }

        return $result;
    }

    public static function getParamNameWithContextClass($paramName, $contextClass)
    {
        if ($contextClass) {
            return invoke_get_class_name($contextClass) . "::" . $paramName;
        }

        return $paramName;
    }

    public static function extractTypes($methodOrClass): array
    {
        $types = new Set();

        $paramsOrProps = [];

        if (is_string($methodOrClass) && class_exists($methodOrClass)) {
            $ref = new \ReflectionClass($methodOrClass);
            $paramsOrProps = $ref->getProperties();
        } else if (is_string($methodOrClass) && function_exists($methodOrClass) || $methodOrClass instanceof Closure) {
            $ref = new \ReflectionFunction($methodOrClass);
            $paramsOrProps = $ref->getParameters();
        }

        $params = ReflectionUtils::reflectionParamsOrPropsToInvoke($paramsOrProps);

        foreach (array_values($params) as $type) {
            $types->add($type);

            $types->add(...static::extractTypes($type));
        }

        return $types->toArray();
    }
}
