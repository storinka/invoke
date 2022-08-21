<?php

namespace InvokeTests\Invoke;

use Invoke\Invoke;
use Invoke\Piping;
use InvokeTests\Invoke\Fixtures\SomeMethod;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;

class InvokeTest extends TestCase
{
//    public function testInvokeCallable()
//    {
//        $callable = function () {
//            assertTrue(true);
//        };
//
//        $invoke = new Invoke();
//        $invoke->setMethod('method', $callable);
//
//        $invoke->invoke('method');
//    }

    public function testInvokeClass()
    {
        $invoke = new Invoke();
        $invoke->setMethod('method', SomeMethod::class);

        $result = $invoke->invoke('method');
        assertEquals(123, $result);

        $result = $invoke->invoke('method', ["param" => 10]);
        assertEquals(133, $result);
    }

    public function testInvokeByPiping()
    {
        $invoke = new Invoke();
        $invoke->setMethod('method', SomeMethod::class);

        $invokeMethod = function (?array $params = null) use ($invoke) {
            return Piping::run($invoke, [
                "name" => "method",
                "params" => $params
            ]);
        };

        assertEquals(123, $invokeMethod());
        assertEquals(133, $invokeMethod(["param" => 10]));
    }
}