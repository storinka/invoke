<?php

use Invoke\InvokeMachine;
use InvokeTests\Functions\Dec2HexFunction;
use InvokeTests\Functions\Dec2HexFunctionV2;
use PHPUnit\Framework\TestCase;

class InvokeMachineTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        InvokeMachine::setup([
            1 => [
                "dec2hex" => Dec2HexFunction::class,
            ],
            2 => [
                "dec2hex" => Dec2HexFunctionV2::class,
            ],
        ]);
    }

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
