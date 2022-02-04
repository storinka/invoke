<?php

namespace Invoke\Newdoc;

use Invoke\Data;

class ValidationDocument extends Data
{
    public string $name;

    public string $description;

    public string $class;

    public array $data;
}