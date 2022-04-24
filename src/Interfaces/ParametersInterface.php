<?php

namespace Invoke\Interfaces;

use Invoke\Pipe;

interface ParametersInterface extends Pipe
{
    public function getParametersPipes(): array;
}