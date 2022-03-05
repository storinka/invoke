<?php

namespace InvokeTests\Container;

use Invoke\Container\InvokeContainer;
use InvokeTests\Container\Fixtures\SampleClass;
use InvokeTests\TestCase;

class BindingsTest extends TestCase
{
    public function testBasicBinding(): void
    {
        $container = new InvokeContainer();

        $this->assertFalse($container->has('abc'));

        $container->factory('abc', function () {
            return 'hello';
        });

        $this->assertTrue($container->has('abc'));
        $this->assertEquals('hello', $container->get('abc'));
    }

    public function testClassBinding(): void
    {
        $container = new InvokeContainer();

        $this->assertFalse($container->has('sampleClass'));
        $container->factory('sampleClass', SampleClass::class);

        $this->assertTrue($container->has('sampleClass'));
        $this->assertInstanceOf(SampleClass::class, $container->get('sampleClass'));
    }

    public function testInstanceBinding(): void
    {
        $container = new InvokeContainer();

        $container->singleton('sampleClass', new SampleClass());

        $instance = $container->get('sampleClass');

        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertSame($instance, $container->get('sampleClass'));
    }
}