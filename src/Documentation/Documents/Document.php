<?php

namespace Invoke\Documentation\Documents;

use Invoke\Data;

class Document extends Data
{
    public function shouldIncludeTypeName(): bool
    {
        return true;
    }
}