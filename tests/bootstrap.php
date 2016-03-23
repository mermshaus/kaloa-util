<?php

if (
    (!$loader = @include __DIR__.'/../../../autoload.php')
    && (!$loader = @include __DIR__.'/../vendor/autoload.php')
) {
    die("You must set up the project dependencies, run the following commands:\n"
        . "curl -s http://getcomposer.org/installer | php\n"
        . "php composer.phar install\n");
}

require_once __DIR__ . '/Kaloa/Tests/Util/TypeSafety/Dummy.php';
