<?php

namespace Kaloa\Util\TypeSafety;

use IllegalArgumentException;

trait TypeSafetyTrait
{
    /**
     * @param string $types
     * @param array $args
     */
    private function ensure($types, array $args)
    {
        if (!is_string($types)) {
            throw new IllegalArgumentException('Type list must be of type string');
        }

        $cnt = count($args);

        if (strlen($types) !== $cnt) {
            throw new IllegalArgumentException('Type list length does not match argument count');
        }

        $i = 0;

        foreach ($args as $arg) {
            switch ($types[$i]) {
                case 'b':
                    if (!is_bool($arg)) {
                        throw new IllegalArgumentException('bool expected');
                    }
                    break;

                case 'f':
                    if (!is_float($arg)) {
                        throw new IllegalArgumentException('float expected');
                    }
                    break;

                case 'i':
                    if (!is_int($arg)) {
                        throw new IllegalArgumentException('int expected');
                    }
                    break;

                case 'r':
                    if (!is_resource($arg)) {
                        throw new IllegalArgumentException('resource expected');
                    }
                    break;

                case 's':
                    if (!is_string($arg)) {
                        throw new IllegalArgumentException('string expected');
                    }
                    break;

                case '-':
                    /* skip */
                    break;

                default:
                    throw new IllegalArgumentException('Unknown type at offset ' . $i . '. One of [bfirs-] expected');
                    break;
            }

            $i++;
        }
    }
}
