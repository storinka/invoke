<?php

namespace InvokeTests\Lib\Functions;

use Invoke\InvokeFunction;
use Invoke\Types;

class Dec2HexFunction extends InvokeFunction
{
    public static function params(): array
    {
        return [
            "dec" => Types::int
        ];
    }

    public function handle(int $dec): string
    {
        return dechex($dec);
    }
}
