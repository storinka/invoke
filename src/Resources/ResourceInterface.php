<?php

namespace Invoke\Resources;

interface ResourceInterface
{
    public function getName(): string;

    public function getRepository(): ResourceRepositoryInterface;

    public function getInput(): string;

    public function getResult(): string;

    public function getMethods(): array;
}