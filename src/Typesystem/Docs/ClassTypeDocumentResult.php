<?php

namespace Invoke\Typesystem\Docs;

use Invoke\Typesystem\Typesystem;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;

class ClassTypeDocumentResult extends TypeDocumentResult
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

        $summary = isset($docblock) ? $docblock->getSummary() : null;
        $description = isset($docblock) ? $docblock->getDescription()->render() : null;

        $params = array_map(fn($type) => Typesystem::getTypeName($type), $class::params());

        return [
            "name" => $name,
            "summary" => $summary,
            "description" => $description,

            "params" => $params,
        ];
    }
}
