<?php

namespace Invoke\Support;

use Invoke\Attributes\NotParameter;
use Invoke\NewMethod\Information\HasParametersInformation;
use Invoke\NewMethod\Information\ParameterInformationInterface;

/**
 * @mixin HasParametersInformation
 */
trait WithCachedParametersInformation
{
    /**
     * @var ParameterInformationInterface[]
     */
    #[NotParameter]
    private array $cachedParametersInformation;

    /**
     * @return ParameterInformationInterface[]
     */
    public function asInvokeGetParametersInformation(): array
    {
        if (!isset($this->cachedParametersInformation)) {
            $this->cachedParametersInformation = $this->asInvokeExtractParametersInformation();
        }

        return $this->cachedParametersInformation;
    }
}