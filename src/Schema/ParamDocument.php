<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Toolkit\Validators\ArrayOf;
use Invoke\Utils\Utils;

class ParamDocument extends Data
{
    public string $name;

    public string $type;

    public bool $isOptional;

    public mixed $defaultValue;

    public ?string $summary;

    public ?string $description;

    #[ArrayOf(ValidatorDocument::class)]
    public array $validators;

    public function render(array $data): array
    {
        $type = $data["type"];
        $validators = $data["validators"];

        return [
            "type" => Utils::getSchemaTypeName($type),
            "validators" => ValidatorDocument::many($validators),
        ];
    }
}
