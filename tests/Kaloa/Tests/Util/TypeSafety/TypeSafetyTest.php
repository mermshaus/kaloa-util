<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Util\TypeSafety;

use Kaloa\Util\TypeSafety as ts;
use Kaloa\Util\TypeSafety\TypeSafety;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class TypeSafetyTest extends TestCase
{
    protected function dummyTestFunction($a, $b, stdClass $obj, $c, $d)
    {
        ts\ensure('is-fb', func_get_args());
    }

    public function testFunction()
    {
        $this->expectNotToPerformAssertions();
        $this->dummyTestFunction(1, '1', new stdClass(), 1.0, true);
    }

    protected function dummyTestClass($a, $b, stdClass $obj, $c, $d)
    {
        $ts = new TypeSafety();
        $ts->ensure('is-fb', func_get_args());
    }

    public function testClass()
    {
        $this->expectNotToPerformAssertions();
        $this->dummyTestClass(1, '1', new stdClass(), 1.0, true);
    }

    public function testResourceParameter()
    {
        $this->expectNotToPerformAssertions();

        $ts = new TypeSafety();

        $ts->ensure('r', array(STDIN));
    }

    public function testTrait()
    {
        $this->expectNotToPerformAssertions();

        $class = new Dummy();

        $class->run(1, '1', new stdClass(), 1.0, true);
    }

    public function testTypesNotStringFail()
    {
        $this->expectException(\InvalidArgumentException::class);

        $ts = new TypeSafety();
        $ts->ensure(42, array());
    }

    public function testWrongArgumentCountFail()
    {
        $this->expectException(\InvalidArgumentException::class);

        $ts = new TypeSafety();
        $ts->ensure('ii', array(42));
    }

    public static function wrongDataTypeFailProvider(): array
    {
        return array(
            array('b', array(1)),
            array('f', array(1)),
            array('i', array('1')),
            array('r', array(1)),
            array('s', array(1))
        );
    }

    #[DataProvider('wrongDatatypeFailProvider')]
    public function testWrongDataTypeFail($types, $args)
    {
        $this->expectException(\InvalidArgumentException::class);

        $ts = new TypeSafety();
        $ts->ensure($types, $args);
    }

    public function testUnknownTypeFail()
    {
        $this->expectException(\InvalidArgumentException::class);

        $ts = new TypeSafety();
        $ts->ensure('x', array(42));
    }
}
