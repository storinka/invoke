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

        return Pipeline::pass($instance, $input);
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
     * @return static[]
     */
    public static function many(iterable $items): array
    {
        if (is_array($items)) {
            return array_map(fn($item) => static::from($item), $items);
        }

        $result = [];

        foreach ($items as $item) {
            $result[] = static::from($item);
        }

        return $result;
    }
}