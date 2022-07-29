<?php

namespace Invoke\Abstractions\Resources;

interface ResourceRepositoryInterface
{
    public function get(): array;
}