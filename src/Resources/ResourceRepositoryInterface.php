<?php

namespace Invoke\Resources;

/**
 * @template I
 * @template R
 */
interface ResourceRepositoryInterface
{
    /**
     * @return iterable<R>
     */
    public function get(): iterable;

    /**
     * @return R
     */
    public function getById(mixed $id): mixed;

    /**
     * @param I $input
     * @return R
     */
    public function create(mixed $input): mixed;

    /**
     * @param mixed $id
     * @param I $input
     * @return R
     */
    public function update(mixed $id, mixed $input): mixed;

    /**
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void;
}