<?php

namespace Invoke\Abstractions;

use Invoke\NewMethod\MethodInterface;
use Invoke\Resources\ResourceRepositoryInterface;

class ResourceGetMethodInterface implements MethodInterface
{
    public function __construct(protected string                      $type,
                                protected ResourceRepositoryInterface $resourceRepository)
    {
    }

    protected function handle(): mixed
    {
        return $this->type::many($this->resourceRepository->get());
    }

    public function pass(mixed $value): mixed
    {
        return $this->handle();
    }
}