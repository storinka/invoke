<?php

namespace Invoke\Pipelines\Http\Data;

use Invoke\Data;
use Invoke\Support\WithReadonlyParams;

class FailedResponseData extends Data
{
    use WithReadonlyParams;

    public readonly int $code;

    public readonly string $error;

    public readonly string $message;
}
