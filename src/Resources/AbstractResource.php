<?php

namespace Invoke\Resources;

use Invoke\Container;
use Invoke\Invoke;

abstract class AbstractResource implements ResourceInterface
{
    public string $name;

    public ResourceRepositoryInterface $repository;

    public string $input;

    public string $result;

    public function getName(): string
    {
        return $this->name;
    }

    public function getRepository(): ResourceRepositoryInterface
    {
        return $this->repository;
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getMethods(): array
    {
        $name = $this->getName();

        return [
            "{$name}.get" => new ResourceGetMethod($this),
        ];
    }

    public function boot(): void
    {
        $invoke = Container::get(Invoke::class);

        foreach ($this->getMethods() as $name => $method) {
            $invoke->setMethod($name, $method);
        }
    }
}