<?php

namespace Invoke;

use Invoke\Pipes\ParamsPipe;

abstract class Method extends ParamsPipe
{
    protected abstract function handle();

    public function pass(mixed $input): mixed
    {
        Invoke::setInputMode(true);

        parent::pass($input);

        Invoke::setInputMode(false);

        return $this->handle();
    }

    public function getTypeName(): string
    {
        return Utils::getMethodNameFromClass(static::class);
    }
}