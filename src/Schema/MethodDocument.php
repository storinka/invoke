<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use ReflectionClass;
use ReflectionException;

class MethodDocument extends Data
{
    public string $name;

    public array $params;

    public string $resultType;

    public ?string $summary;

    public ?string $description;

    public array $tags;

    /**
     * @throws ReflectionException
     */
    public function render(array $data): array
    {
        $method = $data["method"];

        $tags = [];

        $reflectionClass = new ReflectionClass($method);

        $comment = ReflectionUtils::extractComment($reflectionClass);
        $summary = $comment["summary"];
        $description = $comment["description"];

        $paramsDocuments = ReflectionUtils::extractParamsPipes($reflectionClass);

        $reflectionMethod = $reflectionClass->getMethod("handle");
        $returnType = ReflectionUtils::extractPipeFromMethodReturnType($reflectionMethod);

        return [
            "summary" => $summary,
            "description" => $description,
            "resultType" => Utils::getSchemaTypeName($returnType),
            "tags" => $tags,

            "params" => ParamDocument::many($paramsDocuments),
        ];
    }
}