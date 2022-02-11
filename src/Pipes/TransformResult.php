<?php

namespace Invoke\Pipes;

use Invoke\Pipe;
use Invoke\Types\SuccessResponseData;

class TransformResult implements Pipe
{
    public function pass(mixed $value): mixed
    {
        return SuccessResponseData::from([
            "result" => $value,
        ]);
    }
}
