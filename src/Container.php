<?php

namespace Invoke;

class Container
{
    public static function make(string $abstract, array $parameters = [])
    {
        return new $abstract;
    }
}