<?php

namespace Invoke\Newdoc;

use Invoke\Data;
use Invoke\Utils\ReflectionUtils;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;

class MethodDocument extends Data
{
    public string $name;

    public array $params;

    /**
     * @throws ReflectionException
     */
    public function render(array $data): array
    {
        $method = $data["method"];

        if (is_string($method) && class_exists($method)) {
            $params = ReflectionUtils::reflectionParamsOrPropsToInvoke((new ReflectionClass($method))->getProperties());
        } else {
            $params = ReflectionUtils::reflectionParamsOrPropsToInvoke((new ReflectionFunction($method))->getParameters());
        }

        $paramsDocuments = [];

        foreach ($params as $paramName => $paramType) {
            $paramsDocuments[] = [
                "name" => $paramName,
                "type" => $paramType
            ];
        }

        return [
            "params" => ParamDocument::many($paramsDocuments),
        ];
    }
}