<?php

namespace Invoke\Documentation\Documents;

use Invoke\Utils\ReflectionUtils;
use Invoke\Validator;
use function Invoke\Utils\get_class_name;

class ValidatorDocument extends Document
{
    public string $name;

    public ?string $summary;

    public ?string $description;

    public array $data;

    public function render(Validator $validator): array
    {
        $data = [];

        if (method_exists($validator, "invoke_getValidatorData")) {
            $data = $validator->invoke_getValidatorData();
        }

        $reflectionClass = ReflectionUtils::getClass($validator::class);
        $comment = ReflectionUtils::extractComment($reflectionClass);
        $summary = $comment["summary"];
        $description = $comment["description"];

        return [
            "name" => get_class_name($validator::class),

            "summary" => $summary,
            "description" => $description,

            "data" => $data,
        ];
    }
}
