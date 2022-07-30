<?php

namespace Invoke\NewMethod\Description;

use Invoke\NewMethod\MethodInterface;

class MethodDescriptionInterfaceImpl implements MethodDescriptionInterface
{
    public function __construct(protected readonly MethodInterface $method)
    {
    }

    public function getCommentedDescription(): CommentedDescriptionInterface
    {
        return new CommentedDescriptionInterfaceImpl($this->method::class);
    }

    public function getParametersDescription(): array
    {
        return [];
    }

    public function getResultTypeDescription(): TypeDescriptionInterface
    {
        return new TypeDescriptionInterfaceImpl($this->method::class);
    }
}