<?php

namespace Invoke\Exceptions;

class InvokeForbiddenException extends InvokeException
{
    public function __construct(string $message = "Forbidden.")
    {
        parent::__construct("FORBIDDEN", $message, 403);
    }
}
