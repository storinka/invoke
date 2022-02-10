<?php

namespace Invoke\Pipes;

use Invoke\Pipe;
use Invoke\Streams\JsonStream;
use Invoke\Types\BinaryType;

class ResultToStream implements Pipe
{
    public function pass(mixed $result): mixed
    {
        if ($result instanceof BinaryType) {
            return $result->getStream();
        }

        $array = $this->toArray($result);
        $json = $this->toJson($array);

        return JsonStream::create($json);
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