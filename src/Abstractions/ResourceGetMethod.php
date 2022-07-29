<?php

namespace Invoke\Abstractions;

use Invoke\Abstractions\Resources\ResourceRepositoryInterface;

class ResourceGetMethod implements MethodInterface
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