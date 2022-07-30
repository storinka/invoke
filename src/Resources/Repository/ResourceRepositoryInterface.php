<?php

namespace Invoke\Resources\Repository;

/**
 * @template I
 * @template T
 */
interface ResourceRepositoryInterface
{
    /**
     * @return T[]
     */
    public function getAll(): array;

    /**
     * @param I $itemInput
     * @return T
     */
    public function create(mixed $itemInput): mixed;

    /**
     * @param mixed $id
     * @param I $itemInput
     * @return T
     */
    public function updateById(mixed $id, mixed $itemInput): mixed;

    /**
     * @param mixed $id
     * @return void
     */
    public function deleteById(mixed $id): mixed;
}