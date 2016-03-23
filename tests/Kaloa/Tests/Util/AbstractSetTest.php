<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Util;

use Kaloa\Tests\Util\FooSet;
use PHPUnit_Framework_TestCase;
use stdClass;

class AbstractSetTest extends PHPUnit_Framework_TestCase
{
    public function testAddingAndRemovingValues()
    {
        $set = new FooSet();

        $set->add(new Foo());

        $this->assertEquals(1, count($set));

        $set[] = new Foo();

        $this->assertEquals(2, count($set));

        unset($set[1]);

        $this->assertEquals(1, count($set));

        $set['foo'] = new Foo();

        $this->assertEquals(2, count($set));
        $this->assertEquals(true, isset($set['foo']));

        unset($set['foo']);

        $this->assertEquals(1, count($set));
        $this->assertEquals(false, isset($set['foo']));
    }

    public function testReadingValues()
    {
        $set = new FooSet();
        $x = new Foo();
        $set['foo'] = new Foo();

        $this->assertEquals($x, $set['foo']);
        $this->assertEquals(null, $set['bar']);

        unset($set['foo']);
        $set['bar'] = $x;

        $this->assertEquals(null, $set['foo']);
        $this->assertEquals($x, $set['bar']);
    }

    public function testAddingWrongValueByMethodFails()
    {
        $this->setExpectedException('InvalidArgumentException');

        $set = new FooSet();

        $set->add(new stdClass());
    }

    public function testAddingWrongValueByArrayAccessFails()
    {
        $this->setExpectedException('InvalidArgumentException');

        $set = new FooSet();

        $set[] = new stdClass();
    }

    public function testForeach()
    {
        $set = new FooSet();
        $set->add(new Foo());
        $set[] = new Foo();

        $i = 0;
        foreach ($set as $foo) {
            $this->assertEquals(true, $foo instanceof Foo);
            $i++;
        }
        $this->assertEquals(2, $i);
    }
}
