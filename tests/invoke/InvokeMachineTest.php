<?php

namespace InvokeTests\Invoke;

use Invoke\Invoke;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class InvokeMachineTest extends TestCase
{
    use SetupInvoke;

    public function testDec2HexCorrectResult()
    {

        $dec = 1123211;

        $result = Invoke::invoke(
            "dec2hex",
            [
                "dec" => $dec,
            ],
        );

        $this->assertEquals(dechex($dec), $result);
    }
}
