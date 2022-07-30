<?php

namespace Invoke\Resources;

use Invoke\Container;
use Invoke\Invoke;

class ResourcesManager
{
    protected array $resources = [];

    protected function getInvoke(): Invoke
    {
        return Container::get(Invoke::class);
    }

    public function addResource(ResourceInterface $resource): void
    {
        $this->resources[] = $resource;

        foreach ($resource->getMethods() as $name => $method) {
            $this->getInvoke()->setMethod($name, $method);
        }
    }

    public function removeResource(ResourceInterface $resource): void
    {
        foreach ($resource->getMethods() as $name => $method) {
            $this->getInvoke()->deleteMethod($name);
        }

        $this->resources[] = array_filter($this->resources[], fn($r) => $r != $resource);
    }
}