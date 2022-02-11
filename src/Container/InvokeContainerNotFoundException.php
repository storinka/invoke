<?php

namespace Invoke\Container;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class InvokeContainerNotFoundException extends Exception implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct("Cannot find dependency: $id");
    }
}
