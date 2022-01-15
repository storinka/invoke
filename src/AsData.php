<?php

namespace Invoke;

interface AsData
{
    public function getDataParams(): array;

    public function toDataArray(): array;
}