<?php

namespace Invoke;

class InvalidVersionException extends InvokeException
{
    public function __construct(int $version)
    {
        parent::__construct("INVALID_VERSION", "Invalid version \"$version\".", 400);
    }
}
