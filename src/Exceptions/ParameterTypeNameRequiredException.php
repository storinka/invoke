<?php

namespace Invoke\Exceptions;

/**
 * You must provide type name for parameter (mostly when using union or typed array).
 */
class ParameterTypeNameRequiredException extends TypeNameRequiredException
{
    public string $path;

    public function __construct(string $path)
    {
        parent::__construct("You must explicitly provide type name for \"{$path}\".");

        $this->path = $path;
    }
}
