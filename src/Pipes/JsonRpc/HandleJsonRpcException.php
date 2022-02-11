<?php

namespace Invoke\Pipes\JsonRpc;

use Invoke\Container;
use Invoke\Pipe;
use Invoke\Stop;
use RuntimeException;
use Throwable;

class HandleJsonRpcException implements Pipe
{
    /**
     * @param Throwable $value
     * @return mixed
     */
    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        if (!($value instanceof Throwable)) {
            throw new RuntimeException("The value for HandleJsonRpcException pipe must be a Throwable.");
        }

        $request = Container::get(JsonRpcRequest::class);

        return JsonRpcResponse::from([
            "error" => [
                "code" => $value->getCode(),
                "name" => $value::class,
                "message" => $value->getMessage(),
            ],
            "id" => $request->id,
        ]);
    }
}
