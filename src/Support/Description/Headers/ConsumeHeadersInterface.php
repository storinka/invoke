<?php

namespace Invoke\Support\Description\Headers;

interface ConsumeHeadersInterface
{
    /**
     * @return HeaderDescriptionInterface[]
     */
    public function getConsumedHeaders(): array;
}