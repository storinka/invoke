<?php

namespace Invoke\Schema;

use Invoke\Container;
use Invoke\Data;
use Invoke\Invoke;
use Invoke\Meta\Parameter;
use Invoke\Toolkit\Validators\ArrayOf;
use Invoke\Utils\Utils;

use function Invoke\Utils\array_unique_by_key;

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

        foreach (Container::get(Invoke::class)->getMethods() as $name => $method) {
            if (is_string($method) && class_exists($method)) {
                $methods[] = [
                    "name" => $name,
                    "class" => $method,
                ];

                array_push($types, ...Utils::extractUsedTypes($method));
            }
        }

        $types = TypeDocument::many($types);
        $types = array_unique_by_key($types, "schemaTypeName");

        $methods = MethodDocument::many($methods);

        return static::from([
            "methods" => $methods,
            "types" => $types,
            "libraryVersion" => Invoke::$version,
        ]);
    }
}
