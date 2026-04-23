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
 * @template K of int|string
 * @template T of object
 * @implements ArrayAccess<K, T>
 * @implements IteratorAggregate<K, T>
 */
abstract class AbstractSet implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var class-string<T>
     */
    protected string $_managedClass;

    /**
     * @var array<T>
     */
    protected array $_container = array();

    /**
     * @param T $obj
     */
    public function add(object $obj): void
    {
        if (!$obj instanceof $this->_managedClass) {
            throw new InvalidArgumentException('Argument has to be of type "'
                    . $this->_managedClass . '"');
        }

        $this->offsetSet(null, $obj);
    }

    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof $this->_managedClass) {
            throw new InvalidArgumentException('Argument has to be of type "'
                    . $this->_managedClass . '"');
        }

        if (null === $offset) {
            $this->_container[] = $value;
        } else {
            $this->_container[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->_container[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->_container[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->_container[$offset] ?? null;
    }

    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->_container);
    }

    public function count(): int
    {
        return count($this->_container);
    }
}
