<?php

namespace Invoke\Newdoc;

use Invoke\Data;

class ParamDocument extends Data
{
    public string $name;

    public TypeDocument $type;

    public function render(array $data): array
    {
        $type = $data["type"];

        return [
            "type" => TypeDocument::from($type),
        ];
    }
}