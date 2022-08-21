<?php

namespace Invoke\NewData;

use ArrayAccess;
use Invoke\NewMethod\Information\HasParametersInformation;
use Invoke\Support\HasToArray;
use Invoke\Type;
use JsonSerializable;

interface DataInterface extends Type,
    HasParametersInformation,
    ArrayAccess,
    HasToArray,
    JsonSerializable
{
    /**
     * @param mixed $input
     * @return static
     */
    public static function from(mixed $input): static;

    /**
     * @param mixed $input
     * @param string $mapFn
     * @return static|null
     */
    public static function nullable(mixed $input, string $mapFn = "from"): ?static;

    /**
     * @param iterable $items
     * @param string $mapFn
     * @return array
     */
    public static function many(iterable $items, string $mapFn = "from"): array;
}