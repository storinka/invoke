<?php

namespace Invoke\Resources;

use Invoke\Resources\Methods\ResourceCreateMethod;
use Invoke\Resources\Methods\ResourceDeleteMethod;
use Invoke\Resources\Methods\ResourceGetMethod;
use Invoke\Resources\Methods\ResourceUpdateMethod;

class ResourceDefaultMethods
{
    public function __construct(protected ResourceInterface $resource)
    {
    }

    public function makeMethods(): array
    {
        $name = $this->resource->getName();

        $methods = [];

        $methods["{$name}.get"] = new ResourceGetMethod($this->resource);
        $methods["{$name}.create"] = new ResourceCreateMethod($this->resource);
        $methods["{$name}.update"] = new ResourceUpdateMethod($this->resource);
        $methods["{$name}.delete"] = new ResourceDeleteMethod($this->resource);

        return $methods;
    }
}