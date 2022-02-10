<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Invoke;
use Invoke\Meta\Parameter;
use Invoke\Utils\Utils;
use Invoke\Validators\ArrayOf;

class SchemaDocument extends Data
{
    #[ArrayOf(MethodDocument::class)]
    public array $methods;

    #[ArrayOf(TypeDocument::class)]
    public array $types;

    #[Parameter]
    public string $libraryVersion;

    public static function current(): static
    {
        $methods = [];
        $types = [];

        foreach (Invoke::getMethods() as $name => $method) {
            if (is_string($method) && class_exists($method)) {
                $methods[] = [
                    "name" => $name,
                    "class" => $method,
                ];

                array_push($types, ...Utils::extractUsedTypes($method));
            }
        }

        $types = TypeDocument::many($types);
        $types = invoke_array_unique_by_key($types, "schemaTypeName");

        $methods = MethodDocument::many($methods);

        return static::from([
            "methods" => $methods,
            "types" => $types,
            "libraryVersion" => Invoke::$libraryVersion,
        ]);
    }
}