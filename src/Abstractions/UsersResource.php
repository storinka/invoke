<?php

namespace Invoke\Abstractions;

class UsersResource
{
    public function getName(): string
    {
        return "users";
    }

    public function getRepository(): ResourceRepositoryInterface
    {
        return new ResourceRepository();
    }

    public function boot(): void
    {
        $this->method($this->getName() . ".get", new ResourceGetMethod($this->getRepository()));
    }
}