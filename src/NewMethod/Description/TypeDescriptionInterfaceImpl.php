<?php

namespace Invoke\NewMethod\Description;

class TypeDescriptionInterfaceImpl implements TypeDescriptionInterface
{
    public function __construct(protected readonly string $className)
    {
    }

    public function getCommentedDescription(): CommentedDescriptionInterface
    {
        return new CommentedDescriptionInterfaceImpl($this->className);
    }

    public function getName(): string
    {
        return $this->className;
    }
}