<?php

namespace Invoke\V1\Docs\Types;

use Invoke\V1\Typesystem\ResultV1;
use Invoke\V1\Typesystem\Types;
use Invoke\V1\Typesystem\Utils\ReflectionUtils;
use ReflectionClass;
use ReflectionException;

class FunctionDocumentResult extends ResultV1
{
    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var string|null $summary
     */
    public ?string $summary;

    /**
     * @var string|null $description
     */
    public ?string $description;

    /**
     * @var TypeDocumentResult[] $params
     */
    public array $params;

    /**
     * @var TypeDocumentResult $result
     */
    public TypeDocumentResult $result;

    /**
     * @return array
     */
    public static function params(): array
    {
        return [
            "params" => Types::ArrayOf(TypeDocumentResult::class),
        ];
    }

    /**
     * @param string $functionName
     * @param class-string $invokeFunction
     * @return FunctionDocumentResult
     * @throws ReflectionException
     */
    public static function createFromInvokeFunction(string $functionName, string $invokeFunction): self
    {
        $reflectionClass = new ReflectionClass($invokeFunction);
        $reflectionMethod = $reflectionClass->getMethod("handle");
        $reflectionReturnType = $reflectionMethod->getReturnType();

        $params = ReflectionUtils::inspectInvokeFunctionReflectionClassParams($reflectionClass);
        foreach ($params as $paramName => $paramType) {
            $params[$paramName] = TypeDocumentResult::createFromInvokeType($paramType);
        }

        $result = TypeDocumentResult::createFromInvokeType(Types::T);

        if ($reflectionReturnType) {
            $resultType = ReflectionUtils::mapReflectionTypeToParamType($reflectionReturnType);

            $result = TypeDocumentResult::createFromInvokeType($resultType);
        }

        $comment = ReflectionUtils::parseComment($reflectionClass);

        return static::create([
            "name" => $functionName,
            "summary" => $comment["summary"],
            "description" => $comment["description"],
            "params" => $params,
            "result" => $result,
        ]);
    }
}
