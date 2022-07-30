<?php

namespace Invoke\NewMethod\Description;

interface CommentedDescriptionInterface
{
    public function getShort(): string;

    public function getFull(): string;
}