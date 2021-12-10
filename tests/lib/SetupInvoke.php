<?php

namespace InvokeTests\Lib;

use Invoke\InvokeMachine;
use InvokeTests\Lib\Functions\Dec2HexFunction;
use InvokeTests\Lib\Functions\Dec2HexFunctionV2;

trait SetupInvoke
{
    protected function setUp(): void
    {
        parent::setUp();

        InvokeMachine::setup(
            [
                "1" => [
                    "dec2hex" => Dec2HexFunction::class,
                ],
                "2" => [
                    "dec2hex" => Dec2HexFunctionV2::class,
                ],
            ],
            [
                "strict" => true,
            ]
        );
    }
}