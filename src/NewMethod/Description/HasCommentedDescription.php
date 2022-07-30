<?php

namespace Invoke\NewMethod\Description;

interface HasCommentedDescription
{
    public function getCommentedDescription(): CommentedDescriptionInterface;
}