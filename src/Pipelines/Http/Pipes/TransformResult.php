<?php

namespace Invoke\Pipelines\Http\Pipes;

use Invoke\Pipe;
use Invoke\Pipelines\Http\Data\SuccessResponseData;

class TransformResult implements Pipe
{
    public function pass(mixed $value): mixed
    {
        return SuccessResponseData::from([
            "result" => $value,
        ]);
    }
}
