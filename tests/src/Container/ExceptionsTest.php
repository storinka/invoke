<?php

namespace InvokeTests\Container;

use Invoke\Container\InvokeContainer;
use Invoke\Container\InvokeContainerNotFoundException;
use InvokeTests\TestCase;

class ExceptionsTest extends TestCase
{

    public function testInvalidGet()
    {
        $container = new InvokeContainer();

        $this->expectExceptionObject(new InvokeContainerNotFoundException('undefined'));
        $container->get('undefined');
    }

}