<?php

namespace Invoke\Pipelines\Http\Pipes;

use Invoke\Container;
use Invoke\Pipe;
use Invoke\Pipelines\Http\Streams\JsonStreamDecorator;
use Invoke\Types\BinaryType;
use Psr\Http\Message\StreamFactoryInterface;

class ResultToStream implements Pipe
{
    public function pass(mixed $result): mixed
    {
        if ($result instanceof BinaryType) {
            return $result->getStream();
        }

        $array = $this->toArray($result);
        $json = $this->toJson($array);

        $streamsFactory = Container::get(StreamFactoryInterface::class);

        $stream = $streamsFactory->createStream($json);

        return new JsonStreamDecorator($stream);
    }

    protected function toArray(mixed $result): array
    {
        return (array)$result;
    }

    protected function toJson(array $data): string
    {
        return json_encode($data);
    }
}
