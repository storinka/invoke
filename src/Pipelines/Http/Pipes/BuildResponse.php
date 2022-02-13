<?php

namespace Invoke\Pipelines\Http\Pipes;

use Invoke\Container;
use Invoke\Pipe;
use Invoke\Pipelines\Http\Streams\JsonStreamDecorator;
use Invoke\Pipelines\Http\Streams\StreamDecorator;
use Invoke\Pipelines\Http\Streams\TextStreamDecorator;
use Invoke\Stop;
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

        $response = Container::get(ResponseInterface::class);

        if (!$response->hasHeader("Content-Type")) {
            if ($stream instanceof JsonStreamDecorator) {
                $response = $response->withHeader("Content-Type", "application/json");
            } elseif ($stream instanceof TextStreamDecorator) {
                $response = $response->withHeader("Content-Type", "text/html");
            }
        }

        if ($stream instanceof StreamDecorator) {
            $stream = $stream->getStream();
        }

        return $response->withBody($stream);
    }
}
