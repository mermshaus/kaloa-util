<?php

namespace Kaloa\Util\TypeSafety;

/**
 * @api
 */
function ensure($types, array $args)
{
    static $obj = null;
    if ($obj === null) $obj = new TypeSafety();
    $obj->ensure($types, $args);
}
