<?php

namespace Invoke\Docs;

use Invoke\Docs\Types\FunctionDocumentResult;
use Invoke\InvokeMachine;

class Docs
{
    public static function getFunctionDocument(string $functionName, string $functionClass): FunctionDocumentResult
    {
        return FunctionDocumentResult::createFromInvokeFunction($functionName, $functionClass);
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
