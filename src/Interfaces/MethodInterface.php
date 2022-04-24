<?php

namespace Invoke\Interfaces;

use Invoke\Pipe;

interface MethodInterface extends ParametersInterface
{
    public function getResultPipe(): Pipe;
}