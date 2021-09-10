<?php

namespace Invoke;

class Versions
{
    public static function semver($version): string
    {
        if (!is_string($version)) {
            $version = (string)$version;
        }

        $parts = explode(".", $version);

        $major = $parts[0] ?? "0";
        $minor = $parts[1] ?? "0";
        $patch = $parts[2] ?? "0";

        return "{$major}.{$minor}.{$patch}";
    }
}