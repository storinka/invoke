<?php

namespace Invoke\Pipelines\Http\Pipes;

use Invoke\Pipe;
use Invoke\Streams\JsonStreamDecorator;
use Invoke\Types\BinaryType;
use Nyholm\Psr7\Stream;

class ResultToStream implements Pipe
{
    public function pass(mixed $result): mixed
    {
        if ($result instanceof BinaryType) {
            return $result->getStream();
        }

        $array = $this->toArray($result);
        $json = $this->toJson($array);

        return new JsonStreamDecorator(Stream::create($json));
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
