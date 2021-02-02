<?php

namespace Invoke\Typesystem\Docs;

use Invoke\Typesystem\Type;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;

class ClassFunctionDocumentResult extends FunctionDocumentResult
{
    public function render(array $func): array
    {
        $name = $func["name"];
        $class = $func["class"];

        $reflection = new ReflectionClass($class);
        $docBlockFactory = DocBlockFactory::createInstance();
        $comment = $reflection->getDocComment();
        if ($comment) {
            $docblock = $docBlockFactory->create($comment);
        }

        $returnType = $reflection->getMethod("handle")->getReturnType();

        $summary = isset($docblock) ? $docblock->getSummary() : null;
        $description = isset($docblock) ? $docblock->getDescription() : null;
        $result = $returnType ? Type::getProperTypeName($returnType->getName()) : null;

        $params = array_map(fn($type) => Type::getProperTypeName($type), $class::params());

        return [
            "name" => $name,
            "summary" => $summary,
            "description" => $description,
            "result" => $result,

            "params" => $params,
        ];
    }
}
