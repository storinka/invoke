<?php

namespace InvokeTests\Invoke;

use Invoke\Invoke;
use InvokeTests\Method\Fixtures\SomeMethod;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;

class InvokeTest extends TestCase
{
    public function testMethodsAssign(): void
    {
        $invoke = new Invoke();

        assertFalse($invoke->hasMethod('someMethod'));

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
}