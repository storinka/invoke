<?php

namespace Invoke;

use Invoke\Utils\ReflectionUtils;
use ReflectionClass;

abstract class Method
{
    protected abstract function handle(): AsData|float|int|string|null|array;

    public function __invoke(array $params): AsData|float|int|string|null|array
    {
        $reflectionClass = new ReflectionClass($this);

        $params = Typesystem::validateParams(
            ReflectionUtils::reflectionParamsOrPropsToInvoke($reflectionClass->getProperties()),
            $params
        );

        foreach ($params as $name => $value) {
            $this->{$name} = $value;
        }

        return $this->handle();
    }
}