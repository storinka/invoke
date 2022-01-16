<?php

namespace Invoke\Validation;

use Invoke\Typesystem;
use Invoke\Validation;

class TypeWithValidations extends Validation
{
    protected mixed $type;

    /** @var Validation[] $validations */
    protected array $validations;

    public function __construct(mixed $type, array $validations)
    {
        $this->validations = $validations;
        $this->type = $type;
    }

    public function validate(string $paramName, $value): mixed
    {
        Typesystem::validateParam($paramName, $this->type, $value);

        foreach ($this->validations as $validation) {
            $value = $validation->validate($paramName, $value);
        }

        return $value;
    }

    public function hasValidation(string $validationClass): bool
    {
        foreach ($this->validations as $validation) {
            if ($validation::class === $validationClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Validation[]
     */
    public function getValidations(): array
    {
        return $this->validations;
    }

    /**
     * @return mixed
     */
    public function getType(): mixed
    {
        return $this->type;
    }
}