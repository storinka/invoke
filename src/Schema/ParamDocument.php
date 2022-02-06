<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Validators\ArrayOf;

class ParamDocument extends Data
{
    public string $name;

    public string $type;

    public bool $isOptional;

    public mixed $defaultValue;

    #[ArrayOf(ValidatorDocument::class)]
    public array $validators;

    public function render(array $data): array
    {
        $type = $data["type"];
        $validators = $data["validators"];

        return [
            "type" => $type->getTypeName(),
            "validators" => ValidatorDocument::many($validators),
        ];
    }
}