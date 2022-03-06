<?php

namespace InvokeTests\Invoke;

use Invoke\Invoke;
use Invoke\Pipelines\ErrorPipeline;
use Invoke\Pipelines\MainPipeline;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;

class ConfigTest extends TestCase
{
    public function testSetAndGet(): void
    {
        $invoke = new Invoke();

        $invoke->setConfig([
            'defined' => 123
        ]);

        assertEquals(123, $invoke->getConfig('defined'));
        assertEquals(456, $invoke->getConfig('undefined', 456));
        assertNull($invoke->getConfig('undefined'));
        assertNull($invoke->getConfig(''));
    }

    public function testDefaults(): void
    {
        $invoke = new Invoke();

        assertEquals('/', $invoke->getConfig('server.pathPrefix'),);
        assertEquals(true, $invoke->getConfig('inputMode.convertStrings'));
        assertEquals(false, $invoke->getConfig('types.alwaysRequireName'));
        assertEquals(false, $invoke->getConfig('types.alwaysReturnName'));
        assertEquals(MainPipeline::class, $invoke->getConfig('serve.defaultPipeline'));
        assertEquals(ErrorPipeline::class, $invoke->getConfig('serve.defaultErrorPipeline'));
        assertEquals(false, $invoke->getConfig('parameters.onlyWithAttribute'));
        assertEquals(true, $invoke->getConfig('methods.usePropertiesAsParameters'));
    }
}