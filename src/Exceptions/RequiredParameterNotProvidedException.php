<?php

namespace Invoke\Exceptions;

/**
 * Required parameter was not provided.
 */
class RequiredParameterNotProvidedException extends PipeException
{
    public string $path;

    public function __construct(string $path)
    {
        parent::__construct("Required parameter \"{$path}\" was not provided.");

        $this->path = $path;
    }
}
