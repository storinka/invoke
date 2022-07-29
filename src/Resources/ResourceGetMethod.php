<?php

namespace Invoke\Resources;

use Invoke\NewMethod\AbstractNewMethod;

class ResourceGetMethod extends AbstractNewMethod
{
    public function __construct(protected readonly ResourceInterface $resource)
    {
    }

    protected function handle()
    {
        $items = $this->resource->getRepository()->get();

        return $this->resource->getResult()::many($items);
    }

    protected function extractParametersInformation(): array
    {
        return [];
    }
}