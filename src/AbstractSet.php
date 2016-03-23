<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Util;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 *
 *
 * @author Marc Ermshaus <marc@ermshaus.org>
 */
abstract class AbstractSet implements ArrayAccess, Countable, IteratorAggregate
{
    protected $_managedClass = '';

    protected $_container = array();

    public function add($obj)
    {
        if (!$obj instanceof $this->_managedClass) {
            throw new InvalidArgumentException('Argument has to be of type "'
                    . $this->_managedClass . '"');
        }

        $this->_container[] = $obj;
    }

    public function offsetSet($offset, $value) {
        $this->_container[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->_container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->_container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->_container[$offset]) ? $this->_container[$offset] : null;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->_container);
    }

    public function count() {
        return count($this->_container);
    }
}
