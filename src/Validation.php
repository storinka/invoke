<?php

namespace Invoke;

use Stringable;

abstract class Validation implements Stringable
{
    public abstract function validate(string $paramName, $value): mixed;
}