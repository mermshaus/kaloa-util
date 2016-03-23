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

/**
 *
 */
class NodeTest extends PHPUnit_Framework_TestCase
{
    public function testIntegrity()
    {
        $node = new Node('demo');

        $this->assertEquals('demo', $node->getContent());
        $this->assertEquals(null, $node->getParent());
        $this->assertEquals(false, $node->hasChildren());

        $node->addChild(new Node('foo'));
        $node->addChild(new Node('bar'));

        $this->assertEquals(true, $node->hasChildren());
        $this->assertEquals(2, count($node->getChildren()));

        $child = new Node('child');
        $node->addChild($child);

        $this->assertEquals($node, $child->getParent());
    }

    public function testDisplay()
    {
        $root = new Node(null);
        $root->addChild(new Node(new Node('foo')));
        $root->addChild(new Node(array()));
        $root->addChild(new Node(42.0));

$expected = <<<'EOT'
null
    Kaloa\Util\Tree\Node
    Array
    42

EOT;

        $this->assertEquals($expected, $root->display());
    }

    /**
     *
     */
    public function testGetNodesByFilter()
    {
        $root = new Node('bar');
        $root->addChild(new Node('foo'));
        $root->addChild(new Node('bar'));

        $node2 = new Node('bar');
        $node2->addChild(new Node('foo'));
        $node2->addChild(new Node('bar'));

        $node3 = new Node('bar');
        $node3->addChild(new Node('foo'));
        $node3->addChild(new Node('bar'));

        $node2->addChild($node3);

        $root->addChild($node2);

        $nodes = $root->getNodesByFilter(function (/*$content*/) {
            return false;
        });

        $this->assertEquals(0, count($nodes));

        $nodes = $root->getNodesByFilter(function ($content) {
            return ('foo' === $content);
        });

        $this->assertEquals(3, count($nodes));

        foreach ($nodes as $node) {
            $this->assertEquals('foo', $node->getContent());
        }
    }
}
