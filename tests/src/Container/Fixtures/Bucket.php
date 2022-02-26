<?php

namespace InvokeTests\Container\Fixtures;

class Bucket
{
    private string $name;

    private mixed $data;

    public function __construct(string $name, mixed $data = 'default-data')
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): string
    {
        return $this->data;
    }
}