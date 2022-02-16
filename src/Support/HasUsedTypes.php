<?php

namespace Invoke\Support;

use Invoke\Type;

/**
 * Says that class has types which should be included in API Document.
 */
interface HasUsedTypes
{
    /**
     * Get types that should be included in API Document.
     *
     * It can be really heavy operation. Should only be used for generating documentation.
     *
     * @return Type[]
     */
    public function invoke_getUsedTypes(): array;
}
