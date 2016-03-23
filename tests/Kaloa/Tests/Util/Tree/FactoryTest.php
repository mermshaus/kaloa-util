<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Util\Tree;

use Kaloa\Util\Tree\Factory;
use PHPUnit_Framework_TestCase;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testIntegrity()
    {
        $factory = new Factory();

        $node = $factory->fromArray(array(array(1, null, 'demo')));

        $this->assertInstanceOf('Kaloa\\Util\\Tree\\Node', $node);
    }
}
