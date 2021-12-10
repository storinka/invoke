<?php

namespace Invoke;

class Versions
{
    public static function semver($version): string
    {
        [$major, $minor, $patch] = Versions::parseSemver($version);

        if (is_null($major)) {
            $major = 0;
        }
        if (is_null($minor)) {
            $minor = 0;
        }
        if (is_null($patch)) {
            $patch = 0;
        }

        return "{$major}.{$minor}.{$patch}";
    }

    public static function parseSemver($version): array
    {
        if (!is_string($version)) {
            $version = (string)$version;
        }

        $parts = explode(".", $version);

        $major = $parts[0] ?? null;
        $minor = $parts[1] ?? null;
        $patch = $parts[2] ?? null;

        if (!is_null($major)) {
            $major = (int)$major;
        }
        if (!is_null($minor)) {
            $minor = (int)$minor;
        }
        if (!is_null($patch)) {
            $patch = (int)$patch;
        }

        return [$major, $minor, $patch];
    }
}