<?php

namespace Kaloa\Util;

use Exception;

/**
 *
 */
class Stack
{
    /** @var array */
    protected $data;

    /** @var int */
    protected $size;

    /**
     *
     */
    public function __construct()
    {
        $this->data = array();
        $this->size = 0;
    }

    /**
     *
     * @param mixed $element
     */
    public function push($element)
    {
        $this->data[] = $element;
        $this->size++;
    }

    /**
     *
     * @return mixed
     * @throws Exception
     */
    public function pop()
    {
        if ($this->size <= 0) {
            throw new Exception('Stack is empty');
        }

        $this->size--;

        return array_pop($this->data);
    }

    /**
     *
     * @return int
     */
    public function size()
    {
        return $this->size;
    }

    /**
     *
     * @return mixed
     * @throws Exception
     */
    public function peek()
    {
        if ($this->size <= 0) {
            throw new Exception('Stack is empty');
        }

        return $this->data[$this->size - 1];
    }
}
