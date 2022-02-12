<?php

namespace Invoke\Extensions;

use Invoke\Method;

/**
 * Method extension interface.
 */
interface MethodExtension extends Extension
{
    public function beforeHandle(Method $method): void;

    public function afterHandle(Method $method, mixed $result): void;
}
