<?php

namespace Invoke\Documentation\Documents;

use Invoke\Toolkit\Validators\ArrayOf;
use Invoke\Utils\Utils;

/**
 * Parameter document.
 */
class ParamDocument extends Document
{
    /**
     * Parameter name.
     *
     * @var string $name
     */
    public string $name;

    /**
     * Parameter short description.
     *
     * @var string|null $summary
     */
    public ?string $summary;

    /**
     * Parameter full description.
     *
     * @var string|null $description
     */
    public ?string $description;

    /**
     * Is parameter optional.
     *
     * @var bool $isOptional
     */
    public bool $isOptional;

    /**
     * Parameter default value.
     *
     * @var mixed $defaultValue
     */
    public mixed $defaultValue;

    /**
     * Parameter type.
     *
     * @var string $type
     */
    public string $type;

    /**
     * Parameter validators.
     *
     * @var array $validators
     */
    #[ArrayOf(ValidatorDocument::class)]
    public array $validators;

    public function render(array $data): array
    {
        $typeName = Utils::getUniqueTypeName($data["type"]);
        $validatorsDocuments = ValidatorDocument::many($data["validators"]);

        return [
            "type" => $typeName,
            "validators" => $validatorsDocuments,
        ];
    }
}
