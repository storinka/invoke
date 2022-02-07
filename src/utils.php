<?php

/**
 * Get class name without namespace.
 *
 * @param string $class
 * @return string
 */
function invoke_get_class_name(string $class): string
{
    $array = explode("\\", $class);
    return array_pop($array);
}

/**
 * Check whether the array is assoc.
 *
 * @param array $array
 * @return bool
 */
function invoke_is_assoc(array $array): bool
{
    $keys = array_keys($array);
    return array_keys($keys) !== $keys;
}


function invoke_dd(...$data)
{
    echo "<pre>";
    var_dump(...$data);
    die();
    echo "</pre>";
}

if (!function_exists("invoke_semver_sort")) {
    function invoke_semver_sort(array $versions)
    {
        usort($versions, function ($version1, $version2) {
            return -1 * version_compare($version1, $version2);
        });
    }
}

if (!function_exists("invoke_closest_semver")) {
    function invoke_closest_semver(string $search, array $versions): ?string
    {
        $versions = array_reverse($versions);
        invoke_semver_sort($versions);

        foreach ($versions as $version) {
            if (version_compare($search, $version, ">=")) {
                return $version;
            }
        }

        return null;
    }
}

if (!function_exists("array_merge_recursive2")) {
    function array_merge_recursive2($paArray1, $paArray2)
    {
        if (!is_array($paArray1) or !is_array($paArray2)) {
            return $paArray2;
        }
        foreach ($paArray2 as $sKey2 => $sValue2) {
            $paArray1[$sKey2] = array_merge_recursive2(@$paArray1[$sKey2], $sValue2);
        }
        return $paArray1;
    }
}

if (!function_exists("class_uses_deep")) {
    function class_uses_deep($class, $autoload = true): array
    {
        $traits = [];
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));
        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }
        return array_unique($traits);
    }
}

function invoke_array_unique_by_key($array, $property): array
{
    $tempArray = array_unique(array_column($array, $property));
    $moreUniqueArray = array_values(array_intersect_key($array, $tempArray));
    return $moreUniqueArray;
}