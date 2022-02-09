<?php

namespace Invoke\Exceptions;

class ValidationFailedException extends PipeException
{
    public function __construct(string $message = "Validation failed.")
    {
        parent::__construct($message);
    }
}