<?php

namespace InvokeTests\Invoke;

use Invoke\Invoke;
use Invoke\Types\AnyType;
use InvokeTests\Invoke\Fixtures\SomeExtension;
use InvokeTests\TestCase;
use Mockery;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

class ExtensionsTest extends TestCase
{
    public function testAssign(): void
    {
        $invoke = new Invoke();

        $return = $invoke->registerExtension(SomeExtension::class);

        assertEquals($invoke, $return);

        assertCount(1, $invoke->getExtensions());
        assertInstanceOf(SomeExtension::class, $invoke->getExtensions()[0]);
    }

    public function testBoot(): void
    {
        $extension = $this->createMock(SomeExtension::class);
        $extension->expects($this->once())->method('boot')
            ->willReturnCallback(function () {
            });

        $invoke = Mockery::mock(Invoke::class);
        $invoke->shouldReceive('getExtensions')->once()->andReturn([$extension]);
        $invoke->makePartial();

        $invoke->serve(AnyType::class);
    }
}