<?php

namespace InvokeTests\Lib;

use Invoke\Invoke;
use InvokeTests\Lib\Functions\Dec2Hex;

trait SetupInvoke
{
    protected function setUp(): void
    {
        parent::setUp();

        Invoke::setup(
            [
                "dec2hex" => Dec2Hex::class,
            ],
            [
                "strict" => true,
            ]
        );
    }
}
