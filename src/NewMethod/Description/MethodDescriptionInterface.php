<?php

namespace Invoke\NewMethod\Description;

interface MethodDescriptionInterface extends HasCommentedDescription
{
    /**
     * @return ParameterDescriptionInterface[]
     */
    public function getParametersDescription(): array;

    /**
     * @return TypeDescriptionInterface
     */
    public function getResultTypeDescription(): TypeDescriptionInterface;
}