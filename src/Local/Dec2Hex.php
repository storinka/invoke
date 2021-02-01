<?php

namespace Invoke\Local;

use Invoke\InvokeFunction;
use Invoke\Typesystem\Type;

class Dec2Hex extends InvokeFunction
{
    public static bool $secured = false;

    public static string $resultType = Type::Array;

    public static function params(): array
    {
        return [
            "dec" => Type::Int,
            "y" => YInput::class,
        ];
    }

    protected function handle(array $params): array
    {
        return [
            "hex" => "0x" . dechex($params["dec"])
        ];
    }
}
