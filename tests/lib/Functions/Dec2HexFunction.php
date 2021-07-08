<?php

namespace InvokeTests\Functions;

use Invoke\InvokeFunction;
use Invoke\Typesystem\Types;

class Dec2HexFunction extends InvokeFunction
{
    public static function params(): array
    {
        return [
            "dec" => Types::Int
        ];
    }

    public function handle(int $dec): string
    {
        return dechex($dec);
    }
}
