<?php

namespace Invoke\Meta;

use Invoke\Type;

/**
 * Says that class has types which should be included to schema.
 */
interface HasUsedTypes
{
    /**
     * @return Type[]
     */
    public function invoke_getUsedTypes(): array;
}
