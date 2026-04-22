<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Util\TypeSafety;

/**
 * @param array<mixed> $args
 */
function ensure(string $types, array $args): void
{
    static $obj = null;

    if (null === $obj) {
        $obj = new TypeSafety();
    }

    $obj->ensure($types, $args);
}
