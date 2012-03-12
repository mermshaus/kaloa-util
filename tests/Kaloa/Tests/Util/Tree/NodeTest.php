<?php

namespace Kaloa\Tests\Util\Tree;

use PHPUnit_Framework_TestCase;
use Kaloa\Util\Tree\Node;

class NodeTest extends PHPUnit_Framework_TestCase
{
    public function testIntegrity()
    {
        $node = new Node('demo');

        $this->assertEquals('demo', $node->getContent());
    }
}
