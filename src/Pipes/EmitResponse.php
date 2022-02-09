<?php

namespace Invoke\Pipes;

use Invoke\Pipe;
use Invoke\Stop;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class EmitResponse implements Pipe
{
    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function pass(mixed $response): mixed
    {
        if ($response instanceof Stop) {
            return $response;
        }

        if (!($response instanceof ResponseInterface)) {
            throw new RuntimeException("The value for WriteResponse pipe must be a ResponseInterface.");
        }

        $responseEmitter = new SapiEmitter();

        $responseEmitter->emit($response);

        return null;
    }
}