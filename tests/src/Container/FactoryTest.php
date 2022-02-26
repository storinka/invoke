<?php

namespace InvokeTests\Container;

use Invoke\Container\InvokeContainer;
use InvokeTests\Container\Fixtures\Bucket;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

class FactoryTest extends TestCase {
    public function testAutoFactory(): void{
        $container = new InvokeContainer();

        $bucket = $container->make(Bucket::class, [
            "name" => "123",
            "data" => "some data"
        ]);

        assertInstanceOf(Bucket::class, $bucket);
        assertSame('123', $bucket->getName());
        assertSame('some data', $bucket->getData());
    }

    public function testClosureFactory(): void
    {
        $container = new InvokeContainer();
        $container->factory(Bucket::class, function ($data){
           return new Bucket('via-closure', $data);
        });

        $bucket = $container->make(Bucket::class, [
            'data' => 'some data',
        ]);

        assertInstanceOf(Bucket::class, $bucket);
        assertSame('via-closure', $bucket->getName());
        assertSame('some data', $bucket->getData());
    }

}