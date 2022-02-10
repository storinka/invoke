<?php

namespace Invoke\Pipelines;

use Invoke\Pipe;
use Invoke\Pipeline;
use Invoke\Pipes\BuildResponse;
use Invoke\Pipes\EmitResponse;
use Invoke\Pipes\HandleException;
use Invoke\Pipes\ResultToStream;

class DefaultErrorPipeline implements Pipe
{
    public array $pipes = [
        HandleException::class,
        ResultToStream::class,
        BuildResponse::class,
        EmitResponse::class,
    ];

    public function pass(mixed $value): mixed
    {
        foreach ($this->pipes as $pipe) {
            $value = Pipeline::pass($pipe, $value);
        }

        return $value;
    }
}