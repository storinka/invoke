<?php

namespace InvokeTests;

use Invoke\Container;
use Invoke\Container\InvokeContainer;
use Invoke\Container\InvokeContainerInterface;
use InvokeTests\Container\Fixtures\SampleClass;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertTrue;

class ContainerTest extends TestCase
{
    public function testEmptyCurrent(): void
    {
        $container = Container::current();

        assertInstanceOf(InvokeContainer::class, $container);
        assertInstanceOf(InvokeContainer::class, $container->get(InvokeContainerInterface::class));
    }

    public function testSetCurrent(): void
    {
        $container = new InvokeContainer();
        $container->singleton('sampleClass', SampleClass::class);

        assertFalse(Container::current()->has('sampleClass'));

        Container::setCurrent($container);

        assertTrue(Container::current()->has('sampleClass'));
    }

    public function testProxyMethods(): void
    {
        Container::current()->singleton('sampleClass', SampleClass::class);

        assertTrue(Container::has('sampleClass'));
        Container::delete('sampleClass');
        Container::singleton('sampleClass', SampleClass::class);
        Container::factory('sampleClass', SampleClass::class);
        Container::make(SampleClass::class);
    }
}