<?php

namespace Invoke;

abstract class AbstractSingletonPipe extends AbstractPipe implements PipeSingleton
{
    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = Container::make(static::class);
        }

        return static::$instance;
    }
}