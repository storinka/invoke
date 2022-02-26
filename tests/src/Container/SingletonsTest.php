<?php

namespace InvokeTests\Container;

use Invoke\Container\InvokeContainer;
use InvokeTests\Container\Fixtures\SampleClass;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

class SingletonsTest extends TestCase {
    public function testSingletonInstance(): void
    {
        $container = new InvokeContainer();
        $container->singleton('sampleClass', $instance = new SampleClass());
        assertSame($instance, $container->get('sampleClass'));
    }

    public function testSingletonToItself(): void
    {
        $container = new InvokeContainer();
        $container->singleton(SampleClass::class, SampleClass::class);

        $sc = $container->get(SampleClass::class);
        assertTrue($container->has(SampleClass::class));
        assertSame($sc, $container->get(SampleClass::class));
    }

    public function testHasInstance(): void
    {
        $container = new InvokeContainer();
        $container->singleton('sampleClass', new SampleClass());

        $this->assertTrue($container->has('sampleClass'));
        $this->assertFalse($container->has('otherClass'));
    }

    public function testSingletonClosure(): void
    {
        $container = new InvokeContainer();

        $instance = new SampleClass();

        $container->singleton('sampleClass', function () use ($instance) {
            return $instance;
        });

        $this->assertSame($instance, $container->get('sampleClass'));
    }

//    public function testSingletonClosureTwice(): void //TODO
//    {
//        $container = new InvokeContainer();
//
//        $container->factory('sampleClass', function () {
//            return new SampleClass();
//        });
//
//        $instance = $container->get('sampleClass');
//
//        $this->assertInstanceOf(SampleClass::class, $instance);
//        $this->assertSame($instance, $container->get('sampleClass'));
//    }

/*
    public function testSingletonFactory(): void //TODO
    {
        $container = new InvokeContainer();

        $container->singleton('sampleClass', [self::class, 'sampleClass']);

        $instance = $container->get('sampleClass');

        $this->assertInstanceOf(SampleClass::class, $instance);
        $this->assertSame($instance, $container->get('sampleClass'));
    }

    private function sampleClass(): SampleClass
    {
        return new SampleClass();
    }
*/

//    public function testDelayedSingleton(): void //TODO
//    {
//        $container = new InvokeContainer();
//        $container->singleton('singleton', 'sampleClass');
//
//        $container->factory('sampleClass', function () {
//            return new SampleClass();
//        });
//
//        $instance = $container->get('singleton');
//
//        $this->assertInstanceOf(SampleClass::class, $instance);
//        $this->assertSame($instance, $container->get('singleton'));
//        $this->assertNotSame($instance, $container->get('sampleClass'));
//    }
}