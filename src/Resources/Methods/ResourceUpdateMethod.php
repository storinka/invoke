<?php

namespace Invoke\Resources\Methods;

use Invoke\Interfaces\MethodInterface;
use Invoke\Pipe;
use Invoke\Resources\ResourceInterface;
use Invoke\Types\IntType;

class ResourceUpdateMethod implements MethodInterface
{
    public function __construct(protected ResourceInterface $resource)
    {
    }

    public function getResultPipe(): ?Pipe
    {
        return $this->resource->getResultType();
    }

    public function getParametersPipes(): array
    {
        $singularName = $this->resource->getSingularName();
        $inputType = $this->resource->getInputType();

        return [
            "id" => new IntType(),
            "$singularName" => $inputType,
        ];
    }

    public function pass(mixed $value): mixed
    {
        $singularName = $this->resource->getSingularName();

        $id = $value["id"];
        $input = $value[$singularName];

        return $this->resource->getRepository()->update($id, $input);
    }
}