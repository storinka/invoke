<?php

namespace Invoke\Resources\Users;

use Invoke\Resources\ResourceRepositoryInterface;

class UsersResourceRepository implements ResourceRepositoryInterface
{
    public function get(): array
    {
        return [
            [
                "id" => 1,
                "name" => "David",
            ],
        ];
    }
}