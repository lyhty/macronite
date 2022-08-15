<?php

namespace Lyhty\Macronite;

/**
 * Checks if static method exists.
 *
 * @param  object|string  $object_or_class
 * @param  string  $method
 * @return bool
 */
function static_method_exists($object_or_class, string $method): bool
{
    if (! method_exists($object_or_class, $method)) {
        return false;
    }

    $reflection = new \ReflectionMethod($object_or_class, $method);

    return $reflection->isStatic();
}
