<?php

namespace Invoke\Docs\Types;

use Invoke\Typesystem\Result;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Utils\ReflectionUtils;
use ReflectionClass;

class FunctionDocumentResult extends Result
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
     * @var ParamDocumentResult[] $params
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
            "params" => Types::ArrayOf(ParamDocumentResult::class),
        ];
    }

    /**
     * @param string $functionName
     * @param class-string $functionClass
     * @return FunctionDocumentResult
     */
    public static function createFromInvokeFunction(string $functionName, string $functionClass): self
    {
        $reflectionClass = new ReflectionClass($functionClass);
        $reflectionMethod = $reflectionClass->getMethod("handle");
        $reflectionReturnType = $reflectionMethod->getReturnType();

        $functionParams = ReflectionUtils::inspectInvokeFunctionReflectionClassParams($reflectionClass);

        $params = [];
        foreach ($functionParams as $paramName => $paramType) {
            $params[] = ParamDocumentResult::createFromNameAndType($paramName, $paramType);
        }

        if ($functionClass::resultType()) {
            $result = TypeDocumentResult::createFromInvokeType($functionClass::resultType());
        } else if ($reflectionReturnType) {
            $resultType = ReflectionUtils::mapReflectionTypeToParamType($reflectionReturnType);

            $result = TypeDocumentResult::createFromInvokeType($resultType);
        } else {
            $result = TypeDocumentResult::createFromInvokeType(Types::T);
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
