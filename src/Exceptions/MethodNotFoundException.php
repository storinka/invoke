<?php

namespace Invoke\Exceptions;

class MethodNotFoundException extends NotFoundException
{
    public function __construct(string $method)
    {
        parent::__construct("Method \"{$method}\" was not found.");
    }
}