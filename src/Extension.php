<?php

namespace Invoke;

abstract class Extension
{
    public function registered()
    {

    }

    public function unregistered()
    {

    }

    public function methodInit(Method $method)
    {

    }

    public function methodBeforeHandle(Method $method, array $params = [])
    {

    }

    public function methodAfterHandle(Method $method, mixed $result)
    {

    }
}