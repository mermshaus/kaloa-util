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
class Node
{
    protected $_content  = null;
    protected $_children = array();
    protected $_parent   = null;

    public function __construct($value)
    {
        $this->_content = $value;
    }

    public function addChild(Node $child)
    {
        $this->_children[] = $child;
        $child->setParent($this);
    }

    public function hasChildren()
    {
        return (count($this->_children) > 0);
    }

    public function setParent(Node $node)
    {
        $this->_parent = $node;
    }

    public function getParent()
    {
        return $this->_parent;
    }

    public function getChildren()
    {
        return $this->_children;
    }

    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Displays all elements from a tree in hierarchic order
     *
     * @param int $level Current level of indentation
     */
    public function display($level = 0)
    {
        $value = $this->getContent();

        if ($value === null) {
            $value = 'null';
        } else if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string) $value;
        } else if (is_object($value)) {
            $value = get_class($value);
        } else if (is_array($value)) {
            $value = 'Array';
        } else {
            //$value = 'Unknown';
        }

        $ret = str_repeat(' ', $level * 4) . $value . "\n";
        $children = $this->getChildren();
        foreach ($children as $child) {
            $ret .= $child->display($level + 1);
        }

        return $ret;
    }

    public function getNodesByFilter(Closure $expr)
    {
        foreach ($this->_children as $child) {
            if ($expr($child->getContent())) {
                return $child;
            } else {
                $test = $child->getNode($expr);
                if ($test !== null) {
                    return $test;
                }
            }
        }

        return null;
    }
}
