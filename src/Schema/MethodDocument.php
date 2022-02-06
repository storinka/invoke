<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Utils\ReflectionUtils;
use ReflectionClass;
use ReflectionException;

class MethodDocument extends Data
{
    public string $name;

    public array $params;

    public TypeDocument $resultType;
//
//    public ?string $summary;
//
//    public ?string $description;
//
//    public array $tags;

    /**
     * @throws ReflectionException
     */
    public function render(array $data): array
    {
        $method = $data["method"];

        $summary = null;
        $description = null;
        $tags = [];

        $reflectionClass = new ReflectionClass($method);

        $paramsDocuments = ReflectionUtils::extractParamsPipes($reflectionClass);

        $reflectionMethod = $reflectionClass->getMethod("handle");
        $reflectionReturnType = $reflectionMethod->getReturnType();

        $comment = ReflectionUtils::extractComment($reflectionClass);

        $summary = $comment["summary"];
        $description = $comment["description"];

        $returnPipe = ReflectionUtils::extractPipeFromReflectionType($reflectionReturnType);

        return [
            "summary" => $summary,
            "description" => $description,
            "resultType" => TypeDocument::from($returnPipe),
            "tags" => $tags,

            "params" => ParamDocument::many($paramsDocuments),
        ];
    }
}