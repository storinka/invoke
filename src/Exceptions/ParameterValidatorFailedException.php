<?php

namespace Invoke\Exceptions;

/**
 * Validator for parameter was failed.
 */
class ParameterValidatorFailedException extends ValidatorFailedException
{
    public string $path;

    public function __construct(string $path,
                                string $message)
    {
        parent::__construct("Validator for \"$path\" failed: {$message}");

        $this->path = $path;
    }
}
