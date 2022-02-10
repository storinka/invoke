<?php

namespace Invoke\Exceptions;

class NotFoundException extends PipeException
{
    public function __construct(string $message = "Not found.")
    {
        parent::__construct($message, 404);
    }

    public function getHttpCode(): int
    {
        return 404;
    }
}