<?php

namespace Invoke\Typesystem\Docs;

use Invoke\Typesystem\Result;
use Invoke\Typesystem\Type;

class FunctionDocumentResult extends Result
{
    public static function params(): array
    {
        return [
            "name" => Type::String,
            "summary" => Type::Null(Type::String),
            "description" => Type::Null(Type::String),
            "result" => Type::Null(Type::String),

            "params" => Type::Null(Type::Map),
        ];
    }
}
