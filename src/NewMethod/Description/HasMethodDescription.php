<?php

namespace Invoke\NewMethod\Description;

interface HasMethodDescription
{
    /**
     * @return MethodDescriptionInterface
     */
    public function asInvokeGetMethodDescription(): MethodDescriptionInterface;
}