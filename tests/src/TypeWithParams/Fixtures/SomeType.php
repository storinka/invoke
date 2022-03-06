<?php

namespace InvokeTests\TypeWithParams\Fixtures;

use Invoke\Attributes\NotParameter;
use Invoke\Container\Inject;
use Invoke\Support\TypeWithParams;
use InvokeTests\Container\Fixtures\SampleClass;
use InvokeTests\TypeWithParams\Fixtures\Pipes\DoubleValuePipe;
use function mb_strtoupper;

class SomeType extends TypeWithParams
{
    public string $name;

    public ?string $nullableContent;

    public int $intWithDefault = 123;

    #[DoubleValuePipe]
    public int $intWithPipe;

    #[Inject]
    public SampleClass $sampleClass;

    #[NotParameter]
    public string $notParameter;

    protected string $protectedNotParameter;

    private string $privateNotParameter;

    public string $parameterWithSetter;

    public function setParameterWithSetter(string $newVal)
    {
        $this->parameterWithSetter = mb_strtoupper($newVal);
    }
}