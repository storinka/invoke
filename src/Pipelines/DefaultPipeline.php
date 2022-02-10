<?php

namespace Invoke\Pipelines;

use Invoke\Invoke;
use Invoke\Pipe;
use Invoke\Pipes\BuildResponse;
use Invoke\Pipes\EmitResponse;
use Invoke\Pipes\HandleRequest;
use Invoke\Pipes\ParseRequest;
use Invoke\Pipes\ResultToStream;
use Invoke\Pipes\TransformResult;

class DefaultPipeline implements Pipe
{
    public array $pipes = [
        ParseRequest::class,
        HandleRequest::class,
        Invoke::class,
        TransformResult::class,
        ResultToStream::class,
        BuildResponse::class,
        EmitResponse::class,
    ];

    public function pass(mixed $value): mixed
    {
        foreach ($this->pipes as $pipe) {
            $value = \Invoke\Pipeline::pass($pipe, $value);
        }

        return $value;
    }
}