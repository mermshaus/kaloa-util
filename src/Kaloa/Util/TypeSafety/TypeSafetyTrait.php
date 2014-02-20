<?php

namespace Kaloa\Util\TypeSafety;

use Exception;

trait TypeSafetyTrait
{
    private function ensureBool()
    {
        foreach (func_get_args() as $param)
            if (!is_bool($param))
                throw new Exception('bool expected');
    }

    private function ensureFloat()
    {
        foreach (func_get_args() as $param)
            if (!is_float($param))
                throw new Exception('float expected');
    }

    private function ensureInt()
    {
        foreach (func_get_args() as $param)
            if (!is_int($param))
                throw new Exception('int expected');
    }

    private function ensureResource()
    {
        foreach (func_get_args() as $param)
            if (!is_resource($param))
                throw new Exception('resource expected');
    }

    private function ensureString()
    {
        foreach (func_get_args() as $param)
            if (!is_string($param))
                throw new Exception('string expected');
    }

    /**
     * @api
     */
    private function ensure($types, array $args)
    {
        $this->ensureString($types);

        $cnt = count($args);

        if (strlen($types) !== $cnt)
            throw new Exception('type count does not match argument count');

        $tmp = str_split($types);

        for ($i = 0; $i < $cnt; $i++)
            switch ($types[$i]) {
                case 'b': $this->ensureBool($args[$i]); break;
                case 'f': $this->ensureFloat($args[$i]); break;
                case 'i': $this->ensureInt($args[$i]); break;
                case 'r': $this->ensureResource($args[$i]); break;
                case 's': $this->ensureString($args[$i]); break;
                case '-': /* skip */ break;
                default: throw new Exception('unknown type at position ' . $i);
            }
    }
}
