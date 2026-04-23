# kaloa/util

## Install

Via Composer:

~~~ bash
$ composer require kaloa/util
~~~


## Requirements

PHP 8.2 or later.


## Testing

~~~ bash
$ vendor/bin/phpunit
~~~

Further quality assurance:

~~~ bash
$ vendor/bin/phpcs --standard=PSR12 ./src
$ vendor/bin/phpmd ./src text codesize,design,naming
$ vendor/bin/phpstan analyse --level=max src
~~~


## Credits

- [Marc Ermshaus](https://www.ermshaus.org/)


## License

The package is published under the MIT License. See [LICENSE](LICENSE) for full license info.
