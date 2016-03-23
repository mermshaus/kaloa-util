<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Util\Tree;

use Closure;

/**
 * A simple tree structure
 *
 * There will always be a single distinct root node
 */
final class Node
{
    private $content  = null;
    private $children = array();
    private $parent   = null;

    public function __construct($value)
    {
        $this->content = $value;
    }

    public function addChild(Node $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    public function hasChildren()
    {
        return (count($this->children) > 0);
    }

    public function setParent(Node $node)
    {
        $this->parent = $node;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * Displays all elements from a tree in hierarchic order
     *
     * @param int $level Current level of indentation
     */
    public function display($level = 0)
    {
        $value = $this->getContent();

        if (null === $value) {
            $value = 'null';
        } elseif (is_object($value)) {
            $value = get_class($value);
        } elseif (is_array($value)) {
            $value = 'Array';
        }

        $ret = str_repeat(' ', $level * 4) . $value . "\n";

        $children = $this->getChildren();

        foreach ($children as $child) {
            $ret .= $child->display($level + 1);
        }

        return $ret;
    }

    /**
     *
     * @param Closure $expr
     * @return array
     */
    public function getNodesByFilter(Closure $expr)
    {
        $nodes = array();

        foreach ($this->children as $child) {
            if ($expr($child->getContent())) {
                $nodes[] = $child;
            }

            foreach ($child->getNodesByFilter($expr) as $node) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }
}
