<?php

namespace Invoke\V1\Docs;

use Invoke\InvokeFunction;
use Invoke\InvokeMachine;
use Invoke\V1\Docs\Types\FunctionDocumentResult;

class Docs
{
    public static function getFunctionDocument(string $functionName, InvokeFunction $invokeFunction): FunctionDocumentResult
    {
        return FunctionDocumentResult::createFromInvokeFunction($functionName, $invokeFunction);
    }

    public static function getAllFunctionsDocuments(?int $version = null): array
    {
        if (!$version) {
            $version = InvokeMachine::version();
        }

        $allFunctionsTree = InvokeMachine::functionsFullTree()[$version];

        $allFunctions = [];

        foreach ($allFunctionsTree as $functionName => $functionClass) {
            $allFunctions[] = Docs::getFunctionDocument($functionName, $functionClass);
        }

        return $allFunctions;
    }
}
