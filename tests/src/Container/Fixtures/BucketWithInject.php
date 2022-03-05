<?php

namespace InvokeTests\Container\Fixtures;

use Invoke\Container\Inject;

class BucketWithInject extends Bucket
{
    #[Inject]
    private SampleClass $sampleClass;

    public function getSampleClass(): SampleClass
    {
        return $this->sampleClass;
    }
}