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
            throw new IllegalArgumentException('string expected');
        }

        $cnt = count($args);

        if (strlen($types) !== $cnt) {
            throw new IllegalArgumentException('type count does not match argument count');
        }

        for ($i = 0; $i < $cnt; $i++) {
            switch ($types[$i]) {
                case 'b':
                    if (!is_bool($args[$i])) {
                        throw new IllegalArgumentException('bool expected');
                    }
                    break;

                case 'f':
                    if (!is_float($args[$i])) {
                        throw new IllegalArgumentException('float expected');
                    }
                    break;

                case 'i':
                    if (!is_int($args[$i])) {
                        throw new IllegalArgumentException('int expected');
                    }
                    break;

                case 'r':
                    if (!is_resource($args[$i])) {
                        throw new IllegalArgumentException('resource expected');
                    }
                    break;

                case 's':
                    if (!is_string($args[$i])) {
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
        }
    }
}
