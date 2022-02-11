<?php

namespace Invoke\Pipes\JsonRpc;

use Invoke\Container;
use Invoke\Invoke;
use Invoke\Pipe;
use Invoke\Stop;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class HandleJsonRpcRequest implements Pipe
{
    public function pass(mixed $request): mixed
    {
        if ($request instanceof Stop) {
            return $request;
        }

        if (!($request instanceof ServerRequestInterface)) {
            throw new RuntimeException("The value for HandleRequest pipe must be a ServerRequestInterface.");
        }

        $invoke = Container::get(Invoke::class);

        $invoke->setInputMode(true);

        $data = JsonRpcRequest::from($request->getParsedBody());

        $invoke->setInputMode(false);

        Container::singleton(JsonRpcRequest::class, $data);

        return [
            "name" => $data->method,
            "params" => $data->params ?? [],
        ];
    }
}