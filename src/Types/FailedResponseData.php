<?php

namespace Invoke\Types;

use Invoke\Data;
use Invoke\Support\ReadonlyParams;

class FailedResponseData extends Data
{
    use ReadonlyParams;

    public readonly int $code;

    public readonly string $error;

    public readonly string $message;
}