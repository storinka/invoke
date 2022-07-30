<?php

namespace Invoke\NewMethod\Description;

interface TypeDescriptionInterface extends HasCommentedDescription
{
    public function getName(): string;
}