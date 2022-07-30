<?php

namespace Invoke\NewMethod;

use Invoke\Container;
use Invoke\Invoke;
use Invoke\Stop;
use RuntimeException;

class NewMethod implements MethodInterface
{
    use NewMethodHelpers;

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        // currently only array of parameters allowed as input
        if (!is_array($value)) {
            throw new RuntimeException("Value passed to method must be an array.");
        }

        // call "beforeValidation" hook on extensions
        Invoke::callMethodExtensionsHook($this, "beforeValidation");

        // get current instance of invoke from container
        $invoke = Container::get(Invoke::class);

        // enable input mode
        $invoke->setInputMode(true);

        // validate input parameters
        $parameters = $this->asInvokeValidateInputParameters($value);

        // disable input mode
        $invoke->setInputMode(false);

        // call "beforeHandle" hook on extensions
        Invoke::callMethodExtensionsHook($this, "beforeHandle");

        $result = $this->handle(...$parameters);

        // call "afterHandle" hook on extensions
        Invoke::callMethodExtensionsHook($this, "afterHandle", [$result]);

        return $result;
    }
}