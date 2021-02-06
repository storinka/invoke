<?php

namespace Invoke\Typesystem\Docs;

use Invoke\Typesystem\Typesystem;
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

        if (!$class::resultType()) {
            $returnType = $reflection->getMethod("handle")->getReturnType();
        }

        $summary = isset($docblock) ? $docblock->getSummary() : null;
        $description = isset($docblock) ? $docblock->getDescription() : null;
        if (!$class::resultType()) {
            $result = $returnType ? Typesystem::getTypeName($returnType->getName()) : null;
        } else {
            $result = Typesystem::getTypeName($class::$resultType);
        }

        $params = array_map(fn($type) => Typesystem::getTypeName($type), $class::params());

        return [
            "name" => $name,
            "summary" => $summary,
            "description" => $description,
            "result" => $result,

            "params" => $params,
        ];
    }
}
