<?php

namespace Invoke\V1\Docs\Types;

use Invoke\V1\Typesystem\ResultV1;
use Invoke\V1\Typesystem\Types;

class FunctionDocumentResult extends ResultV1
{
    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var string|null $summary
     */
    public ?string $summary;

    /**
     * @var string|null $description
     */
    public ?string $description;

    /**
     * @var TypeDocumentResult[] $params
     */
    public array $params;

    /**
     * @return array
     */
    public static function params(): array
    {
        return [
            "params" => Types::ArrayOf(TypeDocumentResult::class),
        ];
    }
}
