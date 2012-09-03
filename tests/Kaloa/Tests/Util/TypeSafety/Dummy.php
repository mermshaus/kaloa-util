<?php

namespace Kaloa\Tests\Util\TypeSafety;

use Kaloa\Util\TypeSafety;

use boolean;
use int;
use float;
use string;
use object;
use resource;
use stdClass;

/**
 *
 */
class Dummy
{
    /** @var TypeSafety */
    protected $typeSafety;

    /**
     *
     * @param TypeSafety $typeSafety
     */
    public function __construct(TypeSafety $typeSafety)
    {
        $this->typeSafety = $typeSafety;
    }

    /**
     *
     * @param int   $int
     * @param float $float
     * @param bool  $bool  A boolean value
     */
    public function docblockArgumentsInSignatureOrder($int, $float, $bool)
    {
        $this->typeSafety->assert();
    }

    /**
     * @param float $float
     * @param bool  $bool  A boolean value
     * @param int   $int
     */
    public function docblockArgumentsNotInSignatureOrder($int, $float, $bool)
    {
        $this->typeSafety->assert();
    }

    /**
     *
     * @param int   $int
     * @param bool  $bool
     */
    public function docblockMissingArgument($int, $float, $bool)
    {
        $this->typeSafety->assert();
    }

    /**
     *
     * @param int $int
     * @param float $float
     * @param string $string
     */
    public function convertComplexCases($int, $float, $string)
    {
        extract($this->typeSafety->convert());

        return is_int($int) && is_float($float) && is_string($string);
    }

    /**
     *
     *
     * This is supposed to generate an Exception because it makes no sense.
     *
     * @param resource $resource
     */
    public function convertToResource($resource)
    {
        extract($this->typeSafety->convert());
    }

    /**
     *
     * @param int $int
     * @param stdClass $obj
     * @param array $array
     * @param bool $bool
     */
    public function typeHints($int, stdClass $obj, array $array, $bool)
    {
        $this->typeSafety->assert();
    }

    /**
     *
     * @param boolean $bool
     * @param int $int
     * @param float $float
     * @param string $string
     * @param array $array
     * @param object $object
     * @param resource $resource
     */
    public function assertTypesAreObjects(
        boolean $bool = null,
        int $int = null,
        float $float = null,
        string $string = null,
        array $array = null,
        object $object = null,
        resource $resource = null
    ) {
        $this->typeSafety->assert();
    }

    /**
     *
     * @param bool $bool
     * @param stdClass $obj
     */
    public function assertDefaultValues(
        $bool = true,
        stdClass $obj = null
    ) {
        $this->typeSafety->assert();
    }

    /**
     *
     * @param boolean $bool
     */
    public function assertWrongDefaultValue($bool = null)
    {
        $this->typeSafety->assert();
    }
}
