<?php

namespace Invoke\Docs\Types;

use Invoke\Typesystemx\Result;
use Invoke\Types;
use Invoke\Utils\ReflectionUtils;
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
        if (function_exists($functionClass)) {
            $reflectionFunction = new \ReflectionFunction($functionClass);
            $reflectionParameters = $reflectionFunction->getParameters();
            $reflectionReturnType = $reflectionFunction->getReturnType();

            $functionParams = ReflectionUtils::reflectionParamsOrPropsToInvoke($reflectionParameters);

            $comment = ReflectionUtils::parseComment($reflectionFunction);
        } else {
            $reflectionClass = new ReflectionClass($functionClass);
            $reflectionMethod = $reflectionClass->getMethod("handle");
            $reflectionReturnType = $reflectionMethod->getReturnType();

            $functionParams = ReflectionUtils::reflectionParamsOrPropsToInvoke($reflectionClass->getProperties());

            $comment = ReflectionUtils::parseComment($reflectionClass);
        }

        $params = [];
        foreach ($functionParams as $paramName => $paramType) {
            $params[] = ParamDocumentResult::createFromNameAndType($paramName, $paramType);
        }

        if (!function_exists($functionClass) && $functionClass::resultType()) {
            $result = TypeDocumentResult::createFromInvokeType($functionClass::resultType());
        } else if ($reflectionReturnType) {
            // todo fix
            $resultType = null;
//            $resultType = ReflectionUtils::mapReflectionTypeToParamType($reflectionReturnType);

            $result = TypeDocumentResult::createFromInvokeType($resultType);
        } else {
            $result = TypeDocumentResult::createFromInvokeType(Types::T);
        }

        return static::from([
            "name" => $functionName,
            "summary" => $comment["summary"],
            "description" => $comment["description"],
            "params" => $params,
            "result" => $result,
        ]);
    }
}
