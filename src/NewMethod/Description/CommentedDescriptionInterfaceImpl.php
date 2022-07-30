<?php

namespace Invoke\NewMethod\Description;

class CommentedDescriptionInterfaceImpl implements CommentedDescriptionInterface
{
    public function __construct(protected readonly string $className)
    {
    }

    public function getShort(): string
    {
        return "";
    }

    public function getFull(): string
    {
        return "";
    }
}