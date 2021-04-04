<?php

namespace Invoke;

class InvalidVersionException extends InvokeError
{
    public function __construct(int $version)
    {
        parent::__construct("INVALID_VERSION", 400);
    }
}
