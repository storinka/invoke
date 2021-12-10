<?php

namespace Invoke\Docs\Types;

use Invoke\Typesystem\Result;

class ParamDocumentResult extends Result
{
    public string $name;

    public TypeDocumentResult $type;

    public static function createFromNameAndType(string $paramName, $paramType)
    {
        return static::from([
            "name" => $paramName,
            "type" => TypeDocumentResult::createFromInvokeType($paramType)
        ]);
    }
}
