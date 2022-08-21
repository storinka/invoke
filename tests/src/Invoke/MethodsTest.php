<?php

namespace InvokeTests\Invoke;

use Invoke\Invoke;
use Invoke\Support\MethodClassProxy;
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

        assertEquals(MethodClassProxy::class, $invoke->getMethod('defined')::class);
        assertTrue($invoke->hasMethod('defined'));
        // todo: check if actual method class is ok

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

        assertEquals(MethodClassProxy::class, $invoke->getMethod('someMethod')::class);
        assertEquals(MethodClassProxy::class, $invoke->getMethod('v2/someMethod2')::class);
        assertEquals(MethodClassProxy::class, $invoke->getMethod('v2/new/someMethod3')::class);
        assertEquals(null, $invoke->getMethod('undefined'));
        // todo: check if actual method class is ok
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