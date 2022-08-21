<?php

namespace Invoke\Resources\ExampleUsers;

use Invoke\Resources\Repository\ResourceRepositoryInterface;

/**
 * @implements ResourceRepositoryInterface<UserInput, array>
 */
class UsersResourceRepository implements ResourceRepositoryInterface
{
    private array $data = [
        [
            "id" => 1,
            "name" => "David",
        ],
        [
            "id" => 2,
            "name" => "Victor",
        ],
    ];

    public function getAll(): array
    {
        return $this->data;
    }

    public function create(mixed $itemInput): mixed
    {
        $newItem = $itemInput->toArray();
        $newItem["id"] = rand();

        $this->data[] = $newItem;

        return $newItem;
    }

    public function updateById(mixed $id, mixed $itemInput): mixed
    {
        return null;
    }

    public function deleteById(mixed $id): mixed
    {
        return null;
    }
}