<?php

namespace Invoke\Pipelines\Http;

use Invoke\Invoke;
use Invoke\Pipeline;
use Invoke\Pipelines\Http\Pipes\BuildResponse;
use Invoke\Pipelines\Http\Pipes\EmitResponse;
use Invoke\Pipelines\Http\Pipes\HandleException;
use Invoke\Pipelines\Http\Pipes\HandleRequest;
use Invoke\Pipelines\Http\Pipes\ParseRequest;
use Invoke\Pipelines\Http\Pipes\ResultToStream;
use Invoke\Pipelines\Http\Pipes\TransformResult;

class HttpPipeline extends Pipeline
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

    protected array $catchPipes = [
        HandleException::class,

        TransformResult::class,
        ResultToStream::class,
        BuildResponse::class,
        EmitResponse::class,
    ];
}