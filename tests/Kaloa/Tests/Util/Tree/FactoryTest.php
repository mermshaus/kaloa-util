<?php

namespace Kaloa\Tests\Util\Tree;

use PHPUnit_Framework_TestCase;
use Kaloa\Util\Tree\Factory;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testIntegrity()
    {
        $factory = new Factory();

        $node = $factory->fromArray(array(array(1, null, 'demo')));

        $this->assertInstanceOf('Kaloa\\Util\\Tree\\Node', $node);
    }
}
