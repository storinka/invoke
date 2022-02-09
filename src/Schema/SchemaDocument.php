<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Invoke;
use Invoke\Utils\Utils;
use Invoke\Validators\ArrayOf;
use ReflectionException;

class SchemaDocument extends Data
{
    #[ArrayOf(MethodDocument::class)]
    public array $methods;

    #[ArrayOf(TypeDocument::class)]
    public array $types;

    /**
     * @throws ReflectionException
     */
    public static function current(): static
    {
        $methods = [];
        $pipes = [];

        foreach (Invoke::getMethods() as $name => $method) {
            if (is_string($method) && class_exists($method)) {
                $methods[] = [
                    "name" => $name,
                    "method" => $method,
                ];

                array_push($pipes, ...Utils::extractUsedTypes($method));
            }
        }

        $pipes = TypeDocument::many($pipes);

        $pipes = invoke_array_unique_by_key($pipes, "schemaTypeName");

        return static::from([
            "methods" => MethodDocument::many($methods),
            "types" => $pipes,
        ]);
    }
}