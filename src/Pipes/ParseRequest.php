<?php

namespace Invoke\Pipes;

use Invoke\Container;
use Invoke\Pipe;
use Invoke\Stop;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

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

        Container::singleton(RequestInterface::class, $request);
        Container::singleton(ServerRequestInterface::class, $request);

        return $request;
    }
}