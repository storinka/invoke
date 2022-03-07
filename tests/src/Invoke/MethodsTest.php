<?php

namespace InvokeTests\Invoke;

use Invoke\Invoke;
use InvokeTests\Method\Fixtures\SomeMethod;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

class MethodsTest extends TestCase
{
    public function testSetAndDeleteMethod(): void
    {
        $invoke = new Invoke();

        assertFalse($invoke->hasMethod('defined'));

        $invoke->setMethod('defined', SomeMethod::class);

        assertEquals(SomeMethod::class, $invoke->getMethod('defined'));
        assertTrue($invoke->hasMethod('defined'));

        $invoke->deleteMethod('defined');

        assertFalse($invoke->hasMethod('defined'));
    }

    public function testSetMethods(): void
    {
        $invoke = new Invoke();

        $invoke->setMethods([
            'someMethod' => SomeMethod::class,
            'v2' => [
                'someMethod2' => SomeMethod::class,
                'new' => [
                    'someMethod3' => SomeMethod::class,
                ]
            ],
        ]);

        assertEquals(SomeMethod::class, $invoke->getMethod('someMethod'));
        assertEquals(SomeMethod::class, $invoke->getMethod('v2/someMethod2'));
        assertEquals(SomeMethod::class, $invoke->getMethod('v2/new/someMethod3'));
        assertEquals(null, $invoke->getMethod('undefined'));
    }

    public function testSetMethodsNonAssoc(): void
    {
        $invoke = new Invoke();

        $invoke->setMethods([
            SomeMethod::class,
            SomeMethod::class
        ]);

        assertTrue($invoke->hasMethod('someMethod'));

    }
}