<?php

namespace Invoke\V1\Docs\Functions;

use Invoke\InvokeMachine;
use Invoke\V1\Docs\Docs;
use Invoke\V1\Docs\Types\FunctionDocumentResult;
use Invoke\V1\InvokeFunctionV1;

/**
 * Get all functions documents.
 *
 * @method static FunctionDocumentResult[] invoke(array $params)
 */
class InvokeDocsGetAllFunctionsFunction extends InvokeFunctionV1
{
    public function handle(?int $version): array
    {
        if (!$version) {
            $version = InvokeMachine::version();
        }

        return Docs::getAllFunctionsDocuments($version);
    }
}
