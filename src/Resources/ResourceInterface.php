<?php

namespace Invoke\Resources;

interface ResourceInterface
{
    public function getName(): string;

    public function getSingularName(): string;

    public function getInputType(): string;

    public function getResultType(): string;

    public function getRepository(): ResourceRepositoryInterface;

    public function getMethods(): array;
}