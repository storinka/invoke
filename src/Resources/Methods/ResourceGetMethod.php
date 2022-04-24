<?php

namespace Invoke\Resources\Methods;

use Invoke\Interfaces\MethodInterface;
use Invoke\Pipe;
use Invoke\Resources\ResourceInterface;

class ResourceGetMethod implements MethodInterface
{
    public function __construct(protected ResourceInterface $resource)
    {
    }

    public function getResultPipe(): ?Pipe
    {
        return new \Invoke\Toolkit\Validators\ArrayOf($this->resource->getResultType());
    }

    public function getParametersPipes(): array
    {
        return [];
    }

    public function pass(mixed $value): mixed
    {
        $input = $this->resource->getInputType();
        $items = $this->resource->getRepository()->get();

        return $input::many($items);
    }
}