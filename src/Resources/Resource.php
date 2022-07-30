<?php

namespace Invoke\Resources;

use Invoke\Resources\Repository\ResourceRepositoryInterface;

class Resource extends AbstractResource
{
    public function __construct(public string                      $name,
                                public ResourceRepositoryInterface $repository,
                                public string                      $input,
                                public string                      $result)
    {
    }
}