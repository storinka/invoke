<?php

namespace Invoke\Resources\Users;

use Invoke\Resources\AbstractResource;

class UsersResource extends AbstractResource
{
    public function __construct()
    {
        $this->name = "users";
        $this->repository = new UsersResourceRepository();
        $this->input = UserInput::class;
        $this->result = UserResult::class;
    }
}