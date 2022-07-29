<?php

namespace Invoke\Abstractions\Description;

interface ParameterDescriptionInterface
{
    public function getParameterName(): string;

    public function getTypeDescription(): TypeDescriptionInterface;
}