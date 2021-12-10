<?php

namespace Invoke\Docs\Functions;

use Invoke\Docs\Docs;
use Invoke\Docs\Types\FunctionDocumentResult;
use Invoke\InvokeFunction;
use Invoke\InvokeMachine;

/**
 * Get all functions documents.
 *
 * @method static FunctionDocumentResult[] invoke(array $params)
 */
class InvokeDocsGetAllFunctionsFunction extends InvokeFunction
{
    public function handle($version): array
    {
        if (!$version) {
            $version = InvokeMachine::version();
        }

        return Docs::getAllFunctionsDocuments($version);
    }
}
