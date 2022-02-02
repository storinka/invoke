<?php

namespace Invoke\Newdoc;

use Invoke\Data;
use Invoke\Invoke;
use Invoke\Validation\ArrayOf;

class SchemaDocument extends Data
{
    #[ArrayOf(MethodDocument::class)]
    public array $methods;

    public static function current(): static
    {
        $methods = [];

        foreach (Invoke::$methods as $name => $method) {
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