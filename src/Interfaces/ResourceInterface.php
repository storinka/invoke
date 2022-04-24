<?php

namespace Invoke\Interfaces;

interface ResourceInterface
{
    public function getName(): string;

    public function getInputType(): string;

    public function getResultType(): string;

    public function getRepository(): ResourceRepositoryInterface;

    public function getMethods(): array;
}