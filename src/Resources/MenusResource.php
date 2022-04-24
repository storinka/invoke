<?php

namespace Invoke\Resources;

class MenuInput extends Data
{
}

class MenuResult extends Data
{
}

class MenusRepository implements ResourceRepositoryInterface
{
    public function get(): iterable
    {
        return [];
    }

    public function create(mixed $input): mixed
    {
        return [];
    }

    public function update(mixed $id, mixed $input): mixed
    {
        return [];
    }

    public function delete(mixed $id): void
    {
    }

    public function getById(mixed $id): mixed
    {
        return null;
    }
}

class MenusResource implements ResourceInterface
{
    protected MenusRepository $menusRepository;

    public function __construct()
    {
        $this->menusRepository = new MenusRepository();
    }

    public function getName(): string
    {
        return "menus";
    }

    public function getSingularName(): string
    {
        return "menu";
    }

    public function getInputType(): string
    {
        return MenuInput::class;
    }

    public function getResultType(): string
    {
        return MenuResult::class;
    }

    public function getRepository(): ResourceRepositoryInterface
    {
        return $this->menusRepository;
    }

    public function getMethods(): array
    {
        return [];
    }
}

$menus = new MenusResource();
