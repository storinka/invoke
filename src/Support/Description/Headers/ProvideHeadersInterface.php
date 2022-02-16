<?php

namespace Invoke\Support\Description\Headers;

interface ProvideHeadersInterface
{
    /**
     * @return HeaderDescriptionInterface[]
     */
    public function getProvidedHeaders(): array;
}