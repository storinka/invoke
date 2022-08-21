<?php

namespace Invoke\Resources;

use Invoke\Data;
use Invoke\Resources\Methods\ResourceGetBasicPageMethod;
use Invoke\Resources\Methods\ResourceGetMethod;
use Invoke\Resources\Repository\ResourceRepositoryHasBasicPaginationInterface;
use Invoke\Resources\Repository\ResourceRepositoryInterface;

abstract class AbstractResource implements ResourceInterface
{
    /**
     * Name of the resource.
     *
     * Example: "users"
     *
     * @var string $name
     */
    public string $name;

    /**
     * Repository for CRUD operations.
     *
     * @var ResourceRepositoryInterface $repository
     */
    public ResourceRepositoryInterface $repository;

    /**
     * Data type of input.
     *
     * @var class-string<Data> $input
     */
    public string $input;

    /**
     * Data type of result.
     *
     * @var class-string<Data> $input
     */
    public string $result;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): ResourceRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * @inheritDoc
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     *
     * @inheritDoc
     */
    public function getMethods(): array
    {
        $name = $this->getName();

        $methods = [
            "{$name}.get" => new ResourceGetMethod($this),
        ];

        if ($this->repository instanceof ResourceRepositoryHasBasicPaginationInterface) {
            $methods["{$name}.getBasicPage"] = new ResourceGetBasicPageMethod($this);
        }

        return $methods;
    }
}