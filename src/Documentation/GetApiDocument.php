<?php

namespace Invoke\Documentation;

use Invoke\Documentation\Documents\ApiDocument;
use Invoke\Invoke;
use Invoke\Meta\NotParameter;
use Invoke\Method;

/**
 * Get current API document.
 */
class GetApiDocument extends Method
{
    #[NotParameter]
    protected Invoke $invoke;

    public function __construct(Invoke $invoke)
    {
        $this->invoke = $invoke;
    }

    protected function handle(): ApiDocument
    {
        return ApiDocument::fromInvoke($this->invoke);
    }
}