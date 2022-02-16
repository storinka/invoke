<?php

namespace Invoke\Exceptions;

/**
 * Validator failed.
 */
class ValidatorFailedException extends PipeException
{
    public function __construct(string $message = "Validator failed.")
    {
        parent::__construct($message);
    }
}
