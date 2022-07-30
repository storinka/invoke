<?php

namespace Invoke\Resources\Repository;

interface ResourceRepositoryHasBasicPaginationInterface
{
    public function getBasicPage(int $perPage, int $page): ResourceRepositoryBasicPageInterface;
}