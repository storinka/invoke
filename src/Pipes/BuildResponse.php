<?php

namespace Invoke\Pipes;

use Invoke\Container;
use Invoke\Pipe;
use Invoke\Stop;
use Invoke\Streams\JsonStream;
use Invoke\Streams\TextStream;
use Invoke\Types\BinaryType;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class BuildResponse implements Pipe
{
    /**
     * @param StreamInterface $stream
     * @return mixed
     */
    public function pass(mixed $stream): mixed
    {
        if ($stream instanceof Stop) {
            return $stream;
        }

        if (!($stream instanceof StreamInterface)) {
            throw new RuntimeException("The value for BuildResponse pipe must be a StreamInterface.");
        }

        $response = new Response();

        Container::singleton(ResponseInterface::class, $response);

        if ($stream instanceof BinaryType) {
            $stream = $stream->getStream();
        }

        if (!$response->hasHeader("Content-Type")) {
            if ($stream instanceof JsonStream) {
                $response = $response->withHeader("Content-Type", "application/json");
            } else if ($stream instanceof TextStream) {
                $response = $response->withHeader("Content-Type", "text/html");
            }
        }

        return $response->withBody($stream);
    }
}