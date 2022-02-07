<?php

namespace Invoke;

/**
 * Says that class has types which should be shown in documentation.
 */
interface HasUsedTypes
{
    /**
     * @return Type[]
     */
    public function getUsedTypes(): array;
}