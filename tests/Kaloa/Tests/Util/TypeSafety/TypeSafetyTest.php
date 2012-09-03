<?php

namespace Kaloa\Tests\Util\TypeSafety;

use PHPUnit_Framework_TestCase;

use Kaloa\Util\TypeSafety;
use Kaloa\Tests\Util\TypeSafety\Dummy;

use stdClass;

/**
 *
 */
class TypeSafetyTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testArgumentsInSignatureOrder()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);
        $dummy->docblockArgumentsInSignatureOrder(1, 1.0, true);
    }

    /**
     *
     */
    public function testArgumentsNotInSignatureOrder()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);
        $dummy->docblockArgumentsNotInSignatureOrder(1, 1.0, true);
    }

    /**
     * @expectedException Kaloa\Util\TypeSafety\WrongTypeException
     */
    public function testException()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);
        $dummy->docblockArgumentsInSignatureOrder(false, 1.0, true);
    }

    /**
     * @expectedException Kaloa\Util\TypeSafety\MissingAnnotationException
     */
    public function testMissingDocblockForArgument()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);
        $dummy->docblockMissingArgument(1, 1.0, true);
    }

    public function testConvertComplexCases()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);

        // Integer
        $this->assertEquals(true, $dummy->convertComplexCases(array(), 1.0, 's'));
        $this->assertEquals(true, $dummy->convertComplexCases(new stdClass(), 1.0, 's'));
        $this->assertEquals(true, $dummy->convertComplexCases($handle = fopen(__FILE__, 'r'), 1.0, 's'));
        fclose($handle);

        // Float
        $this->assertEquals(true, $dummy->convertComplexCases(1, array(), 's'));
        $this->assertEquals(true, $dummy->convertComplexCases(1, new stdClass(), 's'));
        $this->assertEquals(true, $dummy->convertComplexCases(1, $handle = fopen(__FILE__, 'r'), 's'));
        fclose($handle);

        // String
        $this->assertEquals(true, $dummy->convertComplexCases(1, 1.0, new stdClass()));
    }

    /**
     * @expectedException Kaloa\Util\TypeSafety\TypeConversionException
     */
    public function testConvertToResourceError()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);

        $dummy->convertToResource('s');
    }

    public function testTypeHints()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);

        $dummy->typeHints(1, new stdClass(), array(), true);
    }

    public function testAssertTypesAreObjects()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);

        $dummy->assertTypesAreObjects(null, null, null, null, null, null, null);
    }

    public function testAssertDefaultValues()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);

        $dummy->assertDefaultValues();
    }

    /**
     * @expectedException Kaloa\Util\TypeSafety\WrongTypeException
     */
    public function assertWrongDefaultValue()
    {
        $t = new TypeSafety();

        $dummy = new Dummy($t);

        $dummy->assertWrongDefaultValue();
    }
}
