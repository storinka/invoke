<?php

namespace Invoke;

use Invoke\NewMethod\MethodInterface;
use Invoke\Support\MethodHelpers;
use Invoke\Utils\ReflectionUtils;
use RuntimeException;

class Method implements MethodInterface
{
    use MethodHelpers;

    public function run(mixed $value): mixed
    {
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
        Invoke::callMethodExtensionsHook($this, "beforeHandle", [$parameters]);

        // call "handle" method
        $result = ReflectionUtils::invokeMethod($this, "handle", array_merge(
            ["CURRENT_PARAMETERS" => $parameters],
            $parameters,
        ), true);

        // call "afterHandle" hook on extensions
        Invoke::callMethodExtensionsHook($this, "afterHandle", [$result]);

        return $result;
    }

    public static function invoke(array $input = []): mixed
    {
        return Piping::run(static::class, $input);
    }
}