<?php

namespace Invoke\Pipelines\Http\Pipes;

use Invoke\Container;
use Invoke\Pipe;
use Invoke\Pipelines\Http\Streams\JsonStreamDecorator;
use Invoke\Pipelines\Http\Streams\StreamDecorator;
use Invoke\Support\HasToArray;
use Psr\Http\Message\StreamFactoryInterface;

class ResultToStream implements Pipe
{
    public function pass(mixed $result): mixed
    {
        if ($result instanceof StreamDecorator) {
            return $result;
        }

        $array = $this->toArray($result);
        $json = $this->toJson($array);

        $streamsFactory = Container::get(StreamFactoryInterface::class);

        $stream = $streamsFactory->createStream($json);

        return new JsonStreamDecorator($stream);
    }

    protected function toArray(mixed $result): array
    {
        if ($result instanceof HasToArray) {
            return $result->toArray();
        }
        
        return is_array($result) ? $result : (array)$result;
    }

    protected function toJson(array $data): string
    {
        return json_encode($data);
    }
}
