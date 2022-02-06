<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Invoke;
use Invoke\Validations\ArrayOf;

class SchemaDocument extends Data
{
    #[ArrayOf(MethodDocument::class)]
    public array $methods;

    public static function current(): static
    {
        $methods = [];

        foreach (Invoke::getMethods() as $name => $method) {
            $methods[] = [
                "name" => $name,
                "method" => $method,
            ];
        }

        return static::from([
            "methods" => MethodDocument::many($methods),
        ]);
    }
}