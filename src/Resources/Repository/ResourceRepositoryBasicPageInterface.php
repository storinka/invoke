<?php

namespace Invoke\Resources\Repository;

interface ResourceRepositoryBasicPageInterface
{
    public function getData(): iterable;

    public function getCurrentPage(): int;

    public function getPerPage(): int;

    public function getTotalCount(): int;
}