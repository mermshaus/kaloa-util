<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Util\TypeSafety;

use Kaloa\Util\TypeSafety\TypeSafetyTrait;
use stdClass;

/**
 *
 */
class Dummy
{
    use TypeSafetyTrait;

    public function run($a, $b, stdClass $obj, $c, $d)
    {
        $this->ensure('is-fb', func_get_args());
    }
}
