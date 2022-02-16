<?php

namespace Invoke\Support\Description\Headers;

use Invoke\Type;

/**
 * General header description interface.
 */
interface HeaderDescriptionInterface
{
    /**
     * Header name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Header summary.
     *
     * @return string|null
     */
    public function getSummary(): ?string;

    /**
     * Header description.
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Header type.
     *
     * @return Type
     */
    public function getType(): Type;

    /**
     * Is header optional.
     *
     * @return bool
     */
    public function isOptional(): bool;
}