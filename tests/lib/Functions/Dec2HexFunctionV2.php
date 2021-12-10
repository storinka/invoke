<?php

namespace InvokeTests\Lib\Functions;

class Dec2HexFunctionV2 extends Dec2HexFunction
{
    public function handle(int $dec): string
    {
        return dechex($dec * 2);
    }
}
