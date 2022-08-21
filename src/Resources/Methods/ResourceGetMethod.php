<?php

namespace Invoke\Resources\Methods;

use Invoke\Method;
use Invoke\Resources\ResourceInterface;

class ResourceGetMethod extends Method
{
    public function __construct(protected readonly ResourceInterface $resource)
    {
    }

    protected function handle()
    {
        $items = $this->resource->getRepository()->getAll();

        return $this->resource->getResult()::many($items);
    }
}