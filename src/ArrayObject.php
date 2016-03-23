<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Util;

use ArrayObject as CoreArrayObject;

/**
 * Adds grouping and multi-dimensional sorting functions to ArrayObject
 *
 * See: http://www.ermshaus.org/2010/03/php-kaloa-spl-arrayobject
 *
 * Please note: This class isn't the most efficient way to perform the
 * implemented operations. A more low-level approach to grouping will be
 * considerably faster and use less resources, sorting operations should be done
 * in the DMBS (if applicable).
 */
final class ArrayObject extends CoreArrayObject
{
    /**
     * Adds possibility to pass multi-dimensional arrays to the constructor
     *
     * All arrays found among the values of the passed array will be transformed
     * recursively to instances of ArrayObject.
     *
     * @param array $array Data array to initialize class with
     */
    public function __construct(array $array)
    {
        foreach($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = new self($value);
            }
        }

        parent::__construct($array);
    }

    /**
     * Groups the array by one or more criteria defined via callback function
     *
     * Each element in the first dimension of the array is passed to the
     * specified callback function and will be reordered in regard to the
     * returned value. This can either be a string with the new key or an array
     * with a stack of new keys. For an element <var>$e</var>, the callback
     * return value <var>array('a', 'b')</var> translates to
     * <var>$newArray['a']['b'][] = $e;</var>.
     *
     * Callback functions may take the element argument by reference and modify
     * it during execution (e. g. to remove any fields that will be grouped by).
     *
     * @param  callback $func Function to group by
     * @return ArrayObject Provides fluent interface
     */
    public function groupBy($func)
    {
        $ret = array();
        $it  = $this->getIterator();

        while ($it->valid()) {

            if (is_object($it->current())) {
                $key = call_user_func($func, $it->current());
            } else {
                // Pass scalar values by reference, too
                $value = $it->current();
                $key = call_user_func_array($func, array(&$value));
                $it->offsetSet($it->key(), $value);
                unset($value);
            }

            if (is_array($key)) {
                $ref = &$ret;

                foreach ($key as $subkey) {
                    if (!array_key_exists($subkey, $ref)) {
                        $ref[$subkey] = array();
                    }
                    $ref = &$ref[$subkey];
                }
                $ref[] = $it->current();
            } else {
                $ret[$key][] = $it->current();
            }

            $it->next();
        }
        unset($ref);

        $ret = new self($ret);
        $this->exchangeArray($ret->getArrayCopy());

        return $this;
    }

    /**
     * Adds usort as an instance method
     *
     * @param callback $cmp_function Function to sort by
     * @return boolean
     */
    public function usort($cmp_function)
    {
        $tmp = $this->getArrayCopy();
        $ret = usort($tmp, $cmp_function);

        $tmp = new self($tmp);
        $this->exchangeArray($tmp->getArrayCopy());

        return $ret;
    }

    /**
     *
     * @param  array|callback $funcs
     * @return ArrayObject Provides fluent interface
     */
    public function usortm($funcs)
    {
        return $this->_uxsortm($funcs);
    }

    /**
     *
     *
     * @param  array|callback $funcs
     * @return ArrayObject Provides fluent interface
     */
    public function uasortm($funcs)
    {
        return $this->_uxsortm($funcs, 'a');
    }

    /**
     *
     * @param  array|callback $funcs
     * @return ArrayObject Provides fluent interface
     */
    public function uksortm($funcs)
    {
        return $this->_uxsortm($funcs, 'k');
    }

    /**
     * Returns the multi-dimensional array structure with all instances of
     * ArrayObject transformed to standard PHP arrays
     *
     * @return array Flattened array
     */
    public function getArrayCopyRec()
    {
        $ret = array();
        $it  = $this->getIterator();

        while ($it->valid()) {
            if ($it->current() instanceof self) {
                $ret[$it->key()] = $it->current()->getArrayCopyRec();
            } else {
                $ret[$it->key()] = $it->current();
            }

            $it->next();
        }

        return $ret;
    }

    /**
     * Recursively applies all provided sorting functions to their corresponding
     * dimension of the array
     *
     * @param ArrayObject $a         Represents the current dimension
     *        in the active array branch
     * @param array                 $sortFuncs Holds the specified sorting
     *        function for each dimension
     * @param int                   $depth     Current dimension
     * @param string                $sortMode  Possible values: 'a', 'k', ''
     *        (= uasort, uksort, usort)
     */
    private function _uxsortmRec(ArrayObject $a, array $sortFuncs,
                                   $depth = 0, $sortMode = '')
    {
        $goOn = (count($sortFuncs) > $depth + 1);
        $it   = $a->getIterator();

        while ($it->valid()) {
            if (null !== $sortFuncs[$depth]) {
                if ($sortMode == 'a') {
                    $it->current()->uasort($sortFuncs[$depth]);
                } else if ($sortMode == 'k') {
                    $it->current()->uksort($sortFuncs[$depth]);
                } else {
                    $it->current()->usort($sortFuncs[$depth]);
                }
            }

            if ($goOn) {
                $this->_uxsortmRec($it->current(), $sortFuncs, $depth + 1,
                                   $sortMode);
            }

            $it->next();
        }
    }

    /**
     * Applies the first sorting function (if set) to the array's first
     * dimension and starts the recursion to apply the other functions (if set)
     *
     * A sorting function is exactly the same as an usort callback. If you don't
     * want to sort a specific dimension but one or more dimensions below it,
     * pass <var>null</var> for each dimension that should be skipped.
     * <var>array(null, null, $func)</var> would sort the third dimension but
     * leave dimensions one and two untouched.
     *
     * @param  array|callback $funcs    Sorting function(s) to sort one or more
     *         dimensions of the array by
     * @param  string         $sortMode Possible values: 'a', 'k', '' (= uasort,
     *         uksort, usort)
     * @return ArrayObject Provides fluent interface
     */
    private function _uxsortm($funcs, $sortMode = '')
    {
        if (!is_array($funcs)) {
            $funcs = array($funcs);
        }

        if (count($funcs) > 0) {
            if (null !== $funcs[0]) {
                if ($sortMode == 'a') {
                    $this->uasort($funcs[0]);
                } else if ($sortMode == 'k') {
                    $this->uksort($funcs[0]);
                } else {
                    $this->usort($funcs[0]);
                }
            }

            if (count($funcs) > 1) {
                $this->_uxsortmRec($this, $funcs, 1, $sortMode);
            }
        }

        return $this;
    }
}
