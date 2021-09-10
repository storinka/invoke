<?php

namespace InvokeTests\Invoke;

use Invoke\InvokeMachine;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class InvokeMachineTest extends TestCase
{
    use SetupInvoke;

    public function testDec2HexFunctionV1RightResult()
    {

        $dec = 1123211;

        $result = InvokeMachine::invoke(
            "dec2hex",
            [
                "dec" => $dec,
            ],
            1
        );

        $this->assertEquals(dechex($dec), $result);
    }

    public function testDec2HexFunctionV2RightResult()
    {
        $dec = 1123211;

        $result = InvokeMachine::invoke(
            "dec2hex",
            [
                "dec" => $dec,
            ],
            2
        );

        $this->assertEquals(dechex($dec * 2), $result);
    }
}
