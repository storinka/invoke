<?php

namespace Invoke\Exceptions;

class ParamValidationFailedException extends ValidationFailedException
{
    public string $path;

    public function __construct(string $path,
                                string $message)
    {
        parent::__construct("Validation for \"$path\" failed: {$message}");

        $this->path = $path;
    }
}
