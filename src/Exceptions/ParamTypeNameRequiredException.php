<?php

namespace Invoke\Exceptions;

class ParamTypeNameRequiredException extends TypeNameRequiredException
{
    public string $path;

    public function __construct(string $path)
    {
        parent::__construct("You must explicitly provide type name for \"{$path}\".");

        $this->path = $path;
    }
}
