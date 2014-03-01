<?php

namespace Kaloa\Util\TypeSafety;

use InvalidArgumentException;

trait TypeSafetyTrait
{
    /**
     * @param string $types
     * @param array $args
     */
    private function ensure($types, array $args)
    {
        if (!is_string($types)) {
            throw new InvalidArgumentException('Type list must be of type string');
        }

        $cnt = count($args);

        if (strlen($types) !== $cnt) {
            throw new InvalidArgumentException('Type list length does not match argument count');
        }

        $i = 0;

        foreach ($args as $arg) {
            switch ($types[$i]) {
                case 'b':
                    if (!is_bool($arg)) {
                        throw new InvalidArgumentException('bool expected');
                    }
                    break;

                case 'f':
                    if (!is_float($arg)) {
                        throw new InvalidArgumentException('float expected');
                    }
                    break;

                case 'i':
                    if (!is_int($arg)) {
                        throw new InvalidArgumentException('int expected');
                    }
                    break;

                case 'r':
                    if (!is_resource($arg)) {
                        throw new InvalidArgumentException('resource expected');
                    }
                    break;

                case 's':
                    if (!is_string($arg)) {
                        throw new InvalidArgumentException('string expected');
                    }
                    break;

                case '-':
                    /* skip */
                    break;

                default:
                    throw new InvalidArgumentException('Unknown type at offset ' . $i . '. One of [bfirs-] expected');
                    break;
            }

            $i++;
        }
    }
}
