<?php

namespace InvokeTests\Lib;

use Invoke\Data;

class UserResult extends Data
{
    public int $id;

    public string $name;

    public bool $isBanned;
}
