<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Util\TypeSafety;

/**
 * @api
 */
class TypeSafety
{
    use TypeSafetyTrait {
        ensure as public;
    }
}
