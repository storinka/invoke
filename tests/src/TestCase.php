<?php

declare(strict_types=1);

namespace InvokeTests;

use Invoke\Container;
use Invoke\Container\InvokeContainer;
use Invoke\Invoke;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;

    protected static array $invokeConfig = [
        "inputMode" => [
            "convertStrings" => true,
        ],
    ];

    protected function setUp(): void
    {
        $invoke = Invoke::create([], self::$invokeConfig);
        $invoke->setInputMode(true); // TODO: drop this
    }

    protected function getContainer(): InvokeContainer
    {
        return Container::current();
    }
}
