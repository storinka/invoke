<?php

namespace Invoke\V1\Docs;

use Invoke\InvokeFunction;
use Invoke\InvokeMachine;
use Invoke\V1\Docs\Types\FunctionDocumentResult;

class Docs
{
    public static function getFunctionDocument(InvokeFunction $invokeFunction): FunctionDocumentResult
    {
        return FunctionDocumentResult::createFromInvokeFunction($invokeFunction);
    }

    public static function getAllFunctionsDocuments(?int $version = null): array
    {
        if (!$version) {
            $version = InvokeMachine::version();
        }

        $allFunctions = InvokeMachine::functionsFullTree()[$version];

        return array_map(
            fn(InvokeFunction $invokeFunction) => Docs::getFunctionDocument($invokeFunction),
            $allFunctions
        );
    }
}
