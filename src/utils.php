<?php

namespace Invoke\Utils {

    use Invoke\Container;
    use Invoke\Invoke;

    /**
     * Show var dump and die.
     *
     * @param ...$data
     * @return void
     */
    function vdd(...$data)
    {
        echo "<pre>";
        var_dump(...$data);
        echo "</pre>";
        die();
    }

    /**
     * Get class name without namespace.
     *
     * @param string $class
     * @return string
     */
    function get_class_name(string $class): string
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
    function is_assoc(array $array): bool
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    /**
     * Internal.
     *
     * @param $paArray1
     * @param $paArray2
     * @return array|mixed
     */
    function array_merge_recursive2($paArray1, $paArray2): mixed
    {
        if (!is_array($paArray1) or !is_array($paArray2)) {
            return $paArray2;
        }
        foreach ($paArray2 as $sKey2 => $sValue2) {
            $paArray1[$sKey2] = array_merge_recursive2(@$paArray1[$sKey2], $sValue2);
        }
        return $paArray1;
    }

    /**
     * Deeply extract class traits.
     *
     * @param $class
     * @param $autoload
     * @return array
     */
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

    /**
     * Filter unique values of array using item key.
     *
     * @param $array
     * @param $property
     * @return array
     */
    function array_unique_by_key($array, $property): array
    {
        $tempArray = array_unique(array_column($array, $property));
        $moreUniqueArray = array_values(array_intersect_key($array, $tempArray));
        return $moreUniqueArray;
    }

    /**
     * Invoke a method.
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    function invoke(string $method, array $params = []): mixed
    {
        $invoke = Container::get(Invoke::class);
        return $invoke->invoke($method, $params);
    }
}
