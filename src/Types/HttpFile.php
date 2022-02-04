<?php

namespace Invoke\Types;

use Invoke\Type;

class HttpFile extends Type
{
    protected string $fileName;

    public function __construct(string $paramName, mixed $value)
    {
        parent::__construct($paramName, $value);

        $this->fileName = $value;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}