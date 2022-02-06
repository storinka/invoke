<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Validations\ArrayOf;

class ParamDocument extends Data
{
    public string $name;

    public TypeDocument $type;

    public bool $isOptional;

    public mixed $defaultValue;

    #[ArrayOf(ValidationDocument::class)]
    public array $validations;

    public function render(array $data): array
    {
        $type = $data["type"];
        $validations = $data["validations"];

        return [
            "type" => TypeDocument::from($type),
            "validations" => ValidationDocument::many($validations),
        ];
    }
}