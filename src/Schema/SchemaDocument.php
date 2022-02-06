<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Invoke;
use Invoke\Utils;
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
            $methods[] = [
                "name" => $name,
                "method" => $method,
            ];

            array_push($pipes, ...Utils::extractPipes($method));
        }

        return static::from([
            "methods" => MethodDocument::many($methods),
            "types" => TypeDocument::many($pipes),
        ]);
    }
}