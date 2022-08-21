<?php

namespace Invoke\Resources\Methods;

use Invoke\Method;
use Invoke\Resources\ResourceInterface;

class ResourceGetBasicPageMethod extends Method
{
    public function __construct(protected readonly ResourceInterface $resource)
    {
    }

    protected function handle(int $perPage = 10,
                              int $page = 1)
    {
        $items = $this->resource->getRepository()->getBasicPage($perPage, $page);

        return $this->resource->getResult()::many($items);
    }
}