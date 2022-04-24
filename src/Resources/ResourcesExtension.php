<?php

namespace Invoke\Resources;

use Invoke\Extensions\Extension;
use Invoke\Invoke;
use Psr\Container\ContainerInterface;

class ResourcesExtension implements Extension
{
    public function boot(Invoke $invoke, ContainerInterface $container): void
    {
        $this->initMethods($invoke, $container);
    }

    public function initMethods(Invoke $invoke, ContainerInterface $container): void
    {
        $resources = $this->getResources($invoke, $container);

        foreach ($resources as $resource) {
            foreach ($resource->getMethods() as $name => $method) {
                $invoke->setMethod($name, $method);
            }
        }
    }

    public function getResources(Invoke $invoke, ContainerInterface $container): iterable
    {
        return $invoke->getConfig("resources", []);
    }
}