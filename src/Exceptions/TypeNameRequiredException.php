<?php

namespace Invoke\Exceptions;

class TypeNameRequiredException extends PipeException
{
    public function __construct(string $message = "You must explicitly provide type name.")
    {
        parent::__construct($message);
    }
}