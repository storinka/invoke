<?php

namespace InvokeTests\Container;

use Invoke\Container\InvokeContainer;
use InvokeTests\Container\Fixtures\BucketWithInject;
use InvokeTests\Container\Fixtures\SampleClass;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertInstanceOf;

class InjectTest extends TestCase
{
    public function testInject()
    {
        $container = new InvokeContainer();
        $container->factory(SampleClass::class, SampleClass::class);
        $container->factory(BucketWithInject::class, BucketWithInject::class);

        $bucket = $container->make(BucketWithInject::class, [
            'name' => "123"
        ]);

        assertInstanceOf(SampleClass::class, $bucket->getSampleClass());
    }
}