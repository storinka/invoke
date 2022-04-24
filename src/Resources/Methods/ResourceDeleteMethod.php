<?php

namespace Invoke\Resources\Methods;

use Invoke\Interfaces\MethodInterface;
use Invoke\Pipe;
use Invoke\Resources\ResourceInterface;
use Invoke\Types\IntType;

class ResourceDeleteMethod implements MethodInterface
{
    public function __construct(protected ResourceInterface $resource)
    {
    }

    public function getResultPipe(): ?Pipe
    {
        return null;
    }

    public function getParametersPipes(): array
    {
        return [
            "id" => new IntType(),
        ];
    }

    public function pass(mixed $value): mixed
    {
        $singularName = $this->resource->getSingularName();

        $id = $value["id"];

        $this->resource->getRepository()->delete($id);

        return null;
    }
}