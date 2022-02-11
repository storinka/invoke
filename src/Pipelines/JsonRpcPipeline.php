<?php

namespace Invoke\Pipelines;

use Invoke\Pipe;
use Invoke\Pipeline;
use Invoke\Pipes\HandleException;
use Invoke\Pipes\HandleRequest;
use Invoke\Pipes\JsonRpc\HandleJsonRpcException;
use Invoke\Pipes\JsonRpc\HandleJsonRpcRequest;

class JsonRpcPipeline implements Pipe
{
    public function pass(mixed $value): mixed
    {
        Pipeline::override(HandleException::class, HandleJsonRpcException::class);
        Pipeline::override(HandleRequest::class, HandleJsonRpcRequest::class);

        return Pipeline::pass(DefaultPipeline::class, $value);
    }
}
