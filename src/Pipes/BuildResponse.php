<?php

namespace Invoke\Pipes;

use Invoke\Container;
use Invoke\Pipe;
use Invoke\Stop;
use Invoke\Streams\JsonStreamDecorator;
use Invoke\Streams\StreamDecorator;
use Invoke\Streams\TextStreamDecorator;
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

        if (!($stream instanceof StreamInterface) && !($stream instanceof StreamDecorator)) {
            throw new RuntimeException("The value for BuildResponse pipe must be a StreamInterface.");
        }

        $response = new Response();

        Container::singleton(ResponseInterface::class, $response);

        if (!$response->hasHeader("Content-Type")) {
            if ($stream instanceof JsonStreamDecorator) {
                $response = $response->withHeader("Content-Type", "application/json");
            } else if ($stream instanceof TextStreamDecorator) {
                $response = $response->withHeader("Content-Type", "text/html");
            }
        }

        if ($stream instanceof StreamDecorator) {
            $stream = $stream->getStream();
        }

        return $response->withBody($stream);
    }
}