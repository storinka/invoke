<?php

namespace Invoke\Exceptions;

/**
 * Method was not registered and not found.
 */
class MethodNotFoundException extends NotFoundException
{
    public function __construct(string $method)
    {
        parent::__construct("Method \"{$method}\" was not found.");
    }
}