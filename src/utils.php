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


function dd(...$data) {
    var_dump(...$data);
    die();
}
