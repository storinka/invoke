<?php

namespace Invoke\NewMethod\Information;

interface HasParametersInformation
{
    /**
     * @return ParameterInformationInterface[]
     */
    public function asInvokeGetParametersInformation(): array;
}