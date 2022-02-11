<?php

declare(strict_types=1);

namespace InvokeTests\Types\Fixtures;

enum StringBackedTestEnum: string
{
    case One = "1";
    case Two = "2";
}
