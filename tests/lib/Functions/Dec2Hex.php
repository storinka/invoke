<?php

namespace InvokeTests\Lib\Functions;

use Invoke\Method;

class Dec2Hex extends Method
{
    public int $dec;

    public function handle(): string
    {
        return dechex($this->dec);
    }
}
