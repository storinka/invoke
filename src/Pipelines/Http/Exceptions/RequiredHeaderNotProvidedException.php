<?php

namespace Invoke\Pipelines\Http\Exceptions;

use Invoke\Exceptions\PipeException;

class RequiredHeaderNotProvidedException extends PipeException
{
    public function __construct(string $header)
    {
        parent::__construct("Required header \"$header\" not provided.", 400);
    }

    public function getHttpCode(): int
    {
        return 400;
    }
}