<?php

namespace Invoke;

use Invoke\Pipes\ParamsPipe;

class Data extends ParamsPipe
{
    /**
     * @param mixed $input
     * @return static
     */
    public static function from(mixed $input): static
    {
        $instance = Container::make(static::class);

        return $instance->pass($input);
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
     * @param array $items
     * @return static[]
     */
    public static function many(array $items): array
    {
        return array_map(fn($item) => static::from($item), $items);
    }
}