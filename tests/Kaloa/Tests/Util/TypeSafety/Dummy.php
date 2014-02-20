<?php

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
