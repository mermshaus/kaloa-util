<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Util\Tree;

use Exception;
use Kaloa\Util\Tree\Node;

/**
 *
 */
final class Factory
{
    /**
     * Creates a tree structure from an array and returns the root element
     *
     * $a has to be a list of (key, parent_key, value) triplets. Keys will not
     * be preserved, they are only used to define the initial tree hierarchy.
     * The value part may be any kind of object, array or scalar value.
     *
     * @param  array $array
     * @return Node Root element
     */
    public function fromArray(array $array)
    {
        $root   = new Node(null);
        $map    = array();
        $map[0] = $root;

        // Create an entry in $map for every item in $a
        foreach ($array as $element) {
            if (3 !== count($element)) {
                throw new Exception('Each array must have 3 elements.');
            }

            $map[$element[0]] = new Node($element[2]);
        }

        //
        foreach ($array as $element) {
            if (empty($element[1])) {
                $element[1] = 0;
            }

            $found = false;
            $i     = 0;
            $keys  = array_keys($map);
            $cnt   = count($keys);
            while (!$found && $i < $cnt) {
                if ($keys[$i] === $element[1]) {
                    $map[$keys[$i]]->addChild($map[$element[0]]);
                    $found = true;
                } else {
                    $i++;
                }
            }
            if (!$found) {
                // Error
                throw new Exception('Data structure does not seem to be consistent. '
                        . 'Key "' . $element[1] . '" could not be found.');
            }
        }

        return $root;
    }
}
