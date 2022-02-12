<?php

namespace Invoke\Schema;

use Invoke\Type;

/**
 * Says that class has types which should be shown in documentation.
 */
interface HasUsedTypes
{
    /**
     * @return Type[]
     */
    public function invoke_getUsedTypes(): array;
}
