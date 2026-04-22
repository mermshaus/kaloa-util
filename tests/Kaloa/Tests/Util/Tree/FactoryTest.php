<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Util\Tree;

use Kaloa\Util\Tree\Factory;
use PHPUnit\Framework\TestCase;
use stdClass;

class FactoryTest extends TestCase
{
    public function testIntegrity()
    {
        $factory = new Factory();

        $node = $factory->fromArray(array(
            array(1, null, 'demo'),
            array(2,    1, new stdClass()),
            array(3,    1, 12.7)
        ));

        $this->assertInstanceOf('Kaloa\\Util\\Tree\\Node', $node);
        $this->assertEquals(true, $node->hasChildren());
        $this->assertEquals(null, $node->getContent());
    }

    public function testCanConstructWithWrongOrder()
    {
        $factory = new Factory();

        $node = $factory->fromArray(array(
            array(3,    1, 'foo'),
            array(2,    1, 'bar'),
            array(1, null, 'baz')
        ));

        $this->assertInstanceOf('Kaloa\\Util\\Tree\\Node', $node);
        $this->assertEquals(true, $node->hasChildren());
    }

    public function testInvalidElementCount()
    {
        $this->expectException(\Exception::class);

        $factory = new Factory();

        $factory->fromArray(array(
            array(1, null)
        ));
    }

    public function testInvalidStructure()
    {
        $this->expectException(\Exception::class);

        $factory = new Factory();

        $factory->fromArray(array(
            array(1, 2, 'foo')
        ));
    }
}
