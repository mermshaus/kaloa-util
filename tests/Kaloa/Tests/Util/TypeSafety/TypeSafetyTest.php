<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Util\TypeSafety;

use Kaloa\Util\TypeSafety as ts;
use Kaloa\Util\TypeSafety\TypeSafety;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 *
 */
class TypeSafetyTest extends PHPUnit_Framework_TestCase
{
    protected function dummyTestFunction($a, $b, stdClass $obj, $c, $d)
    {
        ts\ensure('is-fb', func_get_args());
    }

    /**
     *
     */
    public function testFunction()
    {
        $this->dummyTestFunction(1, '1', new stdClass(), 1.0, true);
    }

    protected function dummyTestClass($a, $b, stdClass $obj, $c, $d)
    {
        $ts = new TypeSafety();
        $ts->ensure('is-fb', func_get_args());
    }

    /**
     *
     */
    public function testClass()
    {
        $this->dummyTestClass(1, '1', new stdClass(), 1.0, true);
    }

    /**
     *
     */
    public function testTrait()
    {
        $class = new Dummy();

        $class->run(1, '1', new stdClass(), 1.0, true);
    }
}
