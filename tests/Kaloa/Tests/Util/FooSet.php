<?php

/*
 * This file is part of the kaloa/util package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Tests\Util;

use Kaloa\Util\AbstractSet;

final class FooSet extends AbstractSet
{
    public function __construct()
    {
        $this->_managedClass = 'Kaloa\\Tests\\Util\\Foo';
    }
}
