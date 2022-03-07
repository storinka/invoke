<?php

namespace InvokeTests\Invoke;

use Invoke\Exceptions\MethodNotFoundException;
use Invoke\Invoke;
use InvokeTests\TestCase;

class ExceptionsTest extends TestCase
{
    public function testInvokeUndefinedMethods()
    {
        $invoke = new Invoke();

        $this->expectExceptionObject(new MethodNotFoundException('undefined'));
        $invoke->invoke('undefined');
    }
}