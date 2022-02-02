<?php

namespace Invoke\Newdoc;

use Invoke\Data;
use Invoke\Types;
use Invoke\Utils\ReflectionUtils;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;

class MethodDocument extends Data
{
    public string $name;

    public array $params;

    public TypeDocument $resultType;

    public ?string $summary;

    public ?string $description;

    public array $tags;

    /**
     * @throws ReflectionException
     */
    public function render(array $data): array
    {
        $method = $data["method"];

        $summary = null;
        $description = null;
        $tags = [];
        $returnType = Types::T;

        if (is_string($method) && class_exists($method)) {
            $params = ReflectionUtils::reflectionParamsOrPropsToInvoke((new ReflectionClass($method))->getProperties());

            $reflectionClass = new ReflectionClass($method);
            $reflectionMethod = $reflectionClass->getMethod("handle");
            $reflectionReturnType = $reflectionMethod->getReturnType();

            $comment = ReflectionUtils::parseComment($reflectionClass);

            $summary = $comment["summary"];
            $description = $comment["description"];

            $returnType = ReflectionUtils::reflectionTypeToInvoke($reflectionReturnType);
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
            "summary" => $summary,
            "description" => $description,
            "resultType" => TypeDocument::from($returnType),
            "tags" => [],

            "params" => ParamDocument::many($paramsDocuments),
        ];
    }
}