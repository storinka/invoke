<?php

namespace Invoke\Typesystem;

abstract class Result extends AbstractType
{
    public static function create($data)
    {
        if (is_null($data)) {
            return null;
        }

        return new static($data);
    }

    public static function createArray($items): array
    {
        return array_map(fn($item) => new static($item), $items);
    }
}
