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
        if (invoke_is_assoc($items)) {
            $items = array_values($items);
        }
        
        return array_map(fn($item) => new static($item), $items);
    }

    public static function wrap(array $array)
    {
        return $array;
    }
}
