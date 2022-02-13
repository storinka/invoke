<?php

namespace Invoke;

use Invoke\Types\TypeWithParams;

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
     * @return ?static
     */
    public static function nullable(mixed $input): ?static
    {
        if ($input === null) {
            return null;
        }

        return static::from($input);
    }

    /**
     * @param iterable $items
     * @param string $mapFn
     * @return static[]
     */
    public static function many(iterable $items, string $mapFn = "from"): array
    {
        if (is_array($items)) {
            return array_map(fn($item) => static::{$mapFn}($item), $items);
        }

        $result = [];

        foreach ($items as $item) {
            $result[] = static::{$mapFn}($item);
        }

        return $result;
    }
}
