<?php

namespace Invoke\Meta;

use Invoke\Type;

/**
 * Says that class has types which should be included to schema.
 */
interface HasUsedTypes
{
    /**
     * It can be really heavy operation. Should only be used for generating documentation.
     *
     * @return Type[]
     */
    public function invoke_getUsedTypes(): array;
}
