<?php

namespace Kaloa\Util\TypeSafety;

/**
 * @api
 */
class TypeSafety
{
    use TypeSafetyTrait {
        ensure as public;
    }
}
