<?php

namespace Kaloa\Tests\Util;

use PHPUnit_Framework_TestCase;
use Kaloa\Util\Stack;

class StackTest extends PHPUnit_Framework_TestCase
{
    public function testIntegrity()
    {
        $stack = new Stack;

        $this->assertEquals(0, $stack->size());

        $stack->push(2);

        $this->assertEquals(2, $stack->peek());
        $this->assertEquals(1, $stack->size());

        $this->assertEquals(2, $stack->pop());
        $this->assertEquals(0, $stack->size());
    }
}
