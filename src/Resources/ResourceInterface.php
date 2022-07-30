<?php

namespace Invoke\Resources;

use Invoke\Data;
use Invoke\Resources\Repository\ResourceRepositoryInterface;

interface ResourceInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return ResourceRepositoryInterface
     */
    public function getRepository(): ResourceRepositoryInterface;

    /**
     * @return class-string<Data>
     */
    public function getInput(): string;

    /**
     * @return class-string<Data>
     */
    public function getResult(): string;

    /**
     * @return array
     */
    public function getMethods(): array;
}