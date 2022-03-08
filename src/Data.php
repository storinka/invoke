<?php

namespace Invoke;

use Invoke\Support\TypeWithParams;

/**
 * Abstract data type pipe.
 *
 * Used to define strictly typed structure.
 */
abstract class Data extends TypeWithParams
{
    /**
     * @param mixed $input
     * @return static
     */
    public static function from(mixed $input): static
    {
        $instance = Container::make(static::class);

        return Piping::run($instance, $input);
    }

    /**
     * @param mixed $input
     * @param string $mapFn
     * @return ?static
     */
    public static function nullable(mixed $input, string $mapFn = "from"): ?static
    {
        if ($input === null) {
            return null;
        }

        return static::{$mapFn}($input);
    }

    /**
     * @param iterable $items
     * @param string $mapFn
     * @return static[]
     */
    public static function many(iterable $items, string $mapFn = "from"): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = static::{$mapFn}($item);
        }

        return $result;
    }
}
