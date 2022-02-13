<?php

namespace Invoke\Pipelines\Http\Pipes;

use Invoke\Container;
use Invoke\Pipe;
use Invoke\Stop;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ParseRequest implements Pipe
{
    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        $psr17Factory = new Psr17Factory();

        $creator = new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

        $request = $creator->fromGlobals();
        $response = $psr17Factory->createResponse();

        Container::singleton(RequestInterface::class, $request);
        Container::singleton(ServerRequestInterface::class, $request);

        Container::singleton(StreamFactoryInterface::class, $psr17Factory);
        Container::singleton(ResponseFactoryInterface::class, $psr17Factory);

        Container::singleton(ResponseInterface::class, $response);

        return $request;
    }
}
