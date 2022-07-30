<?php

namespace Invoke\NewMethod\Description;

interface ParameterDescriptionInterface extends HasCommentedDescription
{
    public function getName(): string;

    public function getTypeDescription(): TypeDescriptionInterface;
}