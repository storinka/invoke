<?php

namespace Invoke\Resources\Methods;

use Invoke\NewMethod\NewMethod;
use Invoke\Resources\ResourceInterface;

class ResourceGetBasicPageMethod extends NewMethod
{
    public function __construct(protected readonly ResourceInterface $resource)
    {
    }

    protected function handle(int $perPage, int $page)
    {
        $items = $this->resource->getRepository()->getBasicPage($perPage, $page);

        return $this->resource->getResult()::many($items);
    }
}