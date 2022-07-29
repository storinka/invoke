<?php

namespace Invoke\NewMethod;

interface HasParameters
{
    /**
     * @return ParameterInformationInterface[]
     */
    public function getParametersInformation(): array;
}