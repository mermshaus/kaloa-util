<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Util\Tree;

use Kaloa\Util\Tree\Node;
use PHPUnit_Framework_TestCase;

class NodeTest extends PHPUnit_Framework_TestCase
{
    public function testIntegrity()
    {
        $node = new Node('demo');

        $this->assertEquals('demo', $node->getContent());
    }
}
