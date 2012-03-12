<?php

namespace Kaloa\Util\Tree;

use Exception;

use Kaloa\Util\Tree\Node;

class Factory
{
    /**
     * Creates a tree structure from an array and returns the root element
     *
     * $a has to be a list of (key, parent_key, value) triplets. Keys will not
     * be preserved, they are only used to define the initial tree hierarchy.
     * The value part may be any kind of object, array or scalar value.
     *
     * @param  array $a
     * @return Node Root element
     */
    public function fromArray(array $a)
    {
        $root   = new Node(null);
        $map    = array();
        $map[0] = $root;

        // Create an entry in $map for every item in $a
        foreach ($a as $ae) {
            $map[$ae[0]] = new Node($ae[2]);
        }

        //
        foreach ($a as $ae) {
            if (empty($ae[1])) {
                $ae[1] = 0;
            }

            $found = false;
            $i     = 0;
            $keys  = array_keys($map);
            $cnt   = count($keys);
            while (!$found && $i < $cnt) {
                if ($keys[$i] === $ae[1]) {
                    $map[$keys[$i]]->addChild($map[$ae[0]]);
                    $found = true;
                } else {
                    $i++;
                }
            }
            if (!$found) {
                // Error
                throw new Exception('Data structure does not seem to be consistent. '
                     . 'Key "' . $ae[1] . '" could not be found.');
            }
        }

        return $root;
    }
}
