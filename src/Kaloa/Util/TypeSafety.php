<?php

namespace Kaloa\Util;

use Kaloa\Util\TypeSafety\MissingAnnotationException;
use Kaloa\Util\TypeSafety\TypeConversionException;
use Kaloa\Util\TypeSafety\WrongTypeException;

use Exception;
use ReflectionClass;
use ReflectionException;

/**
 *
 *
 * <h3>Some explanations</h3>
 *
 * <ul>
 *
 * <li>
 * Read <a href="http://nikic.github.com/2012/03/06/Scalar-type-hinting-is-harder-than-you-think.html">this
 * article by Nikita Popov</a> to get a general feeling for the environment this
 * class has to deal with.
 * </li>
 *
 * <li>
 * <i>int</i>, <i>bool</i>, ... are <em>not</em>
 * <a href="http://www.php.net/manual/en/reserved.keywords.php">reserved
 * keywords</a> in PHP. That means you can define <code>class int {}</code>.
 * That makes a docblock type hint like <code>\@param int $varname</code>
 * point to an object not to the scalar type.
 * </li>
 *
 * <li>
 * A new notation syntax like <code>\@param \<int\>; $varname</code> (note the
 * angle brackets) is out of the question because it probably wouldn't work with
 * most docblock clients from IDEs to documentation tools.
 * </li>
 *
 * <li>
 * <p>So here is what this class will do:</p>
 * <ol>
 *   <li><code>/** \@param int $var * / function f($var) {}</code>
 *         --\>  integer:$var</li>
 *   <li><code>/** \@param int $var * / function f(int $var) {}</code>
 *         --\>  object:$var</li>
 * </ol>
 *
 * <p>If this class encounters an "int" docblock type hint that is not matched
 * by a syntactical type hint (1), we will assume <i>integer</i> <em>even</em>
 * if there  is a class <code>int</code>.</p>
 *
 * <p>If there is a matching syntactical type hint (2), things are simple
 * because with current PHP versions that has to point to a class.</p>
 * </li>
 *
 * <li>
 * Only the following six data types (and two aliases) will be interpreted as
 * distinct docblock type hints.
 *
 * <pre><code>bool|boolean, int|integer, float, string, array, resource</code></pre>
 *
 * <p><em>Everything else</em> will be converted to <i>object</i>.</p>
 *
 * <p><strong>Note:</strong> We will <em>not</em> accept <i>double</i> although
 * <code>gettype()</code> and the internals of this class work with it. The
 * reason is a limitation/bc issue with <code>gettype()</code>. But there is no
 * <i>double</i> data type. <i>double</i> docblock type hints will be converted
 * to <i>object</i>.</p>
 * </li>
 *
 * </ul>
 *
 *
 * <h3>Data types in PHP</h3>
 *
 * <p>http://de3.php.net/manual/en/language.types.intro.php</p>
 *
 * <table>
 * <tr>
 *   <th>Name</th>
 *   <th>Class</th>
 *   <th>gettype()</th>
 *   <th>Type casts</th>
 *   <th>Type hint</th>
 *   <th>Type checking functions</th>
 * </tr>
 * <tr>
 *   <td>bool, boolean</td>
 *   <td>scalar</td>
 *   <td>"boolean"</td>
 *   <td>(bool), (boolean)</td>
 *   <td>&nbsp;</td>
 *   <td>is_bool, is_scalar</td>
 * </tr>
 * <tr>
 *   <td>int, integer (long)</td>
 *   <td>scalar</td>
 *   <td>"integer"</td>
 *   <td>(int), (integer)</td>
 *   <td>&nbsp;</td>
 *   <td>is_int (is_integer, is_long), is_scalar</td>
 * </tr>
 * <tr>
 *   <td>float (double, real)</td>
 *   <td>scalar</td>
 *   <td>"double"</td>
 *   <td>(float), (double), (real)</td>
 *   <td>&nbsp;</td>
 *   <td>is_float (is_double, is_real), is_scalar</td>
 * </tr>
 * <tr>
 *   <td>string</td>
 *   <td>scalar</td>
 *   <td>"string"</td>
 *   <td>(string)</td>
 *   <td>&nbsp;</td>
 *   <td>is_string, is_scalar</td>
 * </tr>
 * <tr>
 *   <td>array</td>
 *   <td>compound</td>
 *   <td>"array"</td>
 *   <td>(array)</td>
 *   <td>array</td>
 *   <td>is_array</td>
 * </tr>
 * <tr>
 *   <td>object</td>
 *   <td>compound</td>
 *   <td>"object"</td>
 *   <td>(object)</td>
 *   <td>&nbsp;</td>
 *   <td>is_object</td>
 * </tr>
 * <tr>
 *   <td>resource</td>
 *   <td>special</td>
 *   <td>"resource"</td>
 *   <td>&nbsp;</td>
 *   <td>&nbsp;</td>
 *   <td>is_resource</td>
 * </tr>
 * <tr>
 *   <td>null, NULL</td>
 *   <td>special</td>
 *   <td>"NULL"</td>
 *   <td>(unset)</td>
 *   <td>&nbsp;</td>
 *   <td>is_null</td>
 * </tr>
 * <tr>
 *   <td>callable</td>
 *   <td>special</td>
 *   <td>&nbsp;</td>
 *   <td>&nbsp;</td>
 *   <td>callable</td>
 *   <td>is_callable</td>
 * </tr>
 * <tr>
 *   <td>mixed</td>
 *   <td>pseudo</td>
 *   <td colspan="4">"mixed indicates that a parameter may accept multiple (but not
 *         necessarily all) types."</td>
 * </tr>
 * <tr>
 *   <td>number</td>
 *   <td>pseudo</td>
 *   <td colspan="4">"number indicates that a parameter can be either integer or float."</td>
 * </tr>
 * <tr>
 *   <td>callback</td>
 *   <td>pseudo</td>
 *   <td colspan="4">"callback pseudo-types was used in this documentation before callable
 *         type hint was introduced by PHP 5.4. It means exactly the same."</td>
 * </tr>
 * <tr>
 *   <td>void</td>
 *   <td>pseudo</td>
 *   <td colspan="4">"void as a return type means that the return value is useless. void in
 *         a parameter list means that the function doesn't accept any
 *         parameters."</td>
 * </tr>
 * <tr>
 *   <td>numeric</td>
 *   <td>(unofficial)</td>
 *   <td>&nbsp;</td>
 *   <td>&nbsp;</td>
 *   <td>&nbsp;</td>
 *   <td>is_numeric</td>
 * </tr>
 * <tr>
 *   <td>binary</td>
 *   <td>(unofficial)</td>
 *   <td>&nbsp;</td>
 *   <td>(binary)</td>
 *   <td>&nbsp;</td>
 *   <td>&nbsp;</td>
 * </tr>
 * </table>
 *
 * <h3>Known issues and limitations</h3>
 *
 * <ul>
 * <li>It is not possible to check for multiple types (int|float, mixed)</li>
 *
 * <li>It is not possible to check for a specifig resource type
 * (resource["file"], resource["mysql link"]). See
 * http://de3.php.net/manual/en/function.get-resource-type.php for details.</li>
 *
 * <li>Call-by-ref and convert() won't work.</li>
 * </ul>
 *
 *
 *
 * <h3>Ideas</h3>
 *
 * <p>Docblock type hints such as array\<Foo\> meaning "must be array of Foo
 * instances". Well, that should rather be modeled as class FooArray.</p>
 */
class TypeSafety
{
    protected $signatureCache = array();

    protected function throwException($methodId, $cacheEntry, $value)
    {
        $expectedType = $cacheEntry['type'];

        if ($cacheEntry['type'] === 'double') {
            $expectedType = 'float';
        }

        $givenType = gettype($value);

        if ($givenType === 'double') {
            $givenType = 'float';
        }

        throw new WrongTypeException(str_replace('.', '::', $methodId) . '() ('
            . $expectedType . ') expected for argument $'
            . $cacheEntry['argument'] . '. ('
            . $givenType . ') given');
    }

    /**
     *
     * @param string $cacheId
     * @param array $data
     * @throws MissingAnnotationException
     */
    protected function buildCache($cacheId, array $data)
    {
        $reflector = new ReflectionClass($data['class']);

        $method = $reflector->getMethod($data['function']);

        $dc = $method->getDocComment();

        $matches = array();

        preg_match_all(
            '~^\s*\*\s*@param\s+(\S+)\s+\$(\S+).*$~m',
            $dc,
            $matches,
            PREG_SET_ORDER
        );

        // Create lookup array so parameter docblocks won't have to be in same
        // order of method signature.
        $formalParameters = array();

        $nameToInstance = array();

        foreach ($method->getParameters() as $o) {
            $formalParameters[] = $o->getName();
            $nameToInstance[$o->getName()] = $o;
        }

        $formalParameters = array_flip($formalParameters);

        $cache = array_fill(0, count($formalParameters), null);

        foreach ($matches as $match) {
            // Convert values to format of gettype() output.
            if ($match[1] === 'bool') {
                $match[1] = 'boolean';
            } elseif ($match[1] === 'int') {
                $match[1] = 'integer';
            } elseif ($match[1] === 'double') {
                $match[1] = 'object';
            } elseif ($match[1] === 'float') {
                $match[1] = 'double';
            }

            // See whether we have a primitive type or an object with class name
            // of primitive type.
            if ($match[1] === 'boolean') {
                try {
                    if ($nameToInstance[$match[2]]->getClass() !== null) {
                        $match[1] = 'object';
                    }
                } catch (ReflectionException $e) {
                    $match[1] = 'object';
                }
            }
            if ($match[1] === 'integer') {
                try {
                    if ($nameToInstance[$match[2]]->getClass() !== null) {
                        $match[1] = 'object';
                    }
                } catch (ReflectionException $e) {
                    $match[1] = 'object';
                }
            }
            if ($match[1] === 'double') {
                try {
                    if ($nameToInstance[$match[2]]->getClass() !== null) {
                        $match[1] = 'object';
                    }
                } catch (ReflectionException $e) {
                    $match[1] = 'object';
                }
            }
            if ($match[1] === 'string') {
                try {
                    if ($nameToInstance[$match[2]]->getClass() !== null) {
                        $match[1] = 'object';
                    }
                } catch (ReflectionException $e) {
                    $match[1] = 'object';
                }
            }
            if ($match[1] === 'resource') {
                try {
                    if ($nameToInstance[$match[2]]->getClass() !== null) {
                        $match[1] = 'object';
                    }
                } catch (ReflectionException $e) {
                    $match[1] = 'object';
                }
            }

            if (!in_array($match[1], array('boolean', 'integer', 'double', 'string', 'array', 'object', 'resource'))) {
                $match[1] = 'object';
            }

            $instance = $nameToInstance[$match[2]];

            $cache[$formalParameters[$match[2]]] = array(
                'type' => $match[1],
                'argument' => $match[2],
                'hasDefault' => $instance->isDefaultValueAvailable(),
                'default' => ($instance->isDefaultValueAvailable()) ? $instance->getDefaultValue() : null
            );
        }

        $tmp = null;
        foreach ($cache as $index => $entry) {
            if ($entry === null) {
                $tmp = ($tmp === null) ? array_flip($formalParameters) : $tmp;

                throw new MissingAnnotationException('Missing docblock annotation for parameter ' . $tmp[$index] . '.');
            }
        }

        $this->signatureCache[$cacheId] = $cache;
    }

    /**
     *
     * @param string $cacheId
     * @param array $args
     * @return array
     * @throws TypeSafeException
     */
    protected function appendDefaultArguments($cacheId, $args)
    {
        $elem = $this->signatureCache[$cacheId];

        for ($i = count($args); $i < count($elem); $i++) {
            if (!$elem[$i]['hasDefault']) {
                throw new TypeSafeException(
                    'Default value for parameter not found.'
                );
            }

            $args[] = $elem[$i]['default'];
        }

        return $args;
    }

    /**
     * Asserts that all arguments of a method have the expected data type.
     *
     * There is no need to run both this method and convert().
     */
    public function assert()
    {
        // This method will be called very often. It is optimized for execution
        // speed. Some code is duplicated for this reason.

        // In PHP 5.4 this might become: $data = debug_backtrace(0, 1)[1];
        $trace = debug_backtrace(0);
        $data = $trace[1];

        !isset($this->signatureCache[$cacheId = $data['class'] . '.' . $data['function']])
            && $this->buildCache($cacheId, $data);

        if (count($this->signatureCache[$cacheId]) !== count($data['args'])) {
            // Append default arguments.
            $data['args'] = $this->appendDefaultArguments($cacheId, $data['args']);
        }

        foreach ($this->signatureCache[$cacheId] as $n => $entry)
            gettype($data['args'][$n]) !== $entry['type']
                // We accept NULL for object and array type. Reasoning: If NULL wasn't
                // acceptable, code would have failed earlier because of a PHP
                // error.
                && !(gettype($data['args'][$n]) === 'NULL'
                    && (
                        ($entry['type'] === 'object' || $entry['type'] === 'array')
                        // Or the default parameter value is NULL. This is
                        // accepted because NULL is some kind of a parameter
                        // skipping instruction until default becomes available.
                        // https://wiki.php.net/rfc/skipparams
                        /**
                         * @todo Decided not to allow this but to wait for the
                         * RFC to be implemented. Returning the correct data
                         * type is the primary goal of this class.
                         */
                        #|| ($entry['type'] !== 'object' && $entry['type'] !== 'array'
                        #    && $entry['hasDefault'] && $entry['default'] === null
                        #)
                    )
                )
                    && $this->throwException($cacheId, $entry, $data['args'][$n]);
    }

    /**
     * Converts all arguments of a method to the expected data type.
     *
     * There is no need to run both this method and assert().
     *
     *   Note:
     *
     *   Some type conversions in PHP lead to an undefined result or a notice or
     *   even an error. This method handles those cases in a way that will
     *   always produce a defined result without any error level output. For
     *   instance, you may pass an object with no __toString() method for a
     *   parameter that expects a string and you will never get any error
     *   output, although passing such an object might be considered a bug from
     *   your application's point of view.
     *
     * @return array Array to use with extract().
     */
    public function convert()
    {
        // This method will be called very often. It is optimized for execution
        // speed. Some code is duplicated for this reason.

        // In PHP 5.4 this might become: $data = debug_backtrace(0, 1)[1];
        $trace = debug_backtrace(0);
        $data = $trace[1];

        !isset($this->signatureCache[$cacheId = $data['class'] . '.' . $data['function']])
            && $this->buildCache($cacheId, $data);

        $overwrites = array();

        foreach ($this->signatureCache[$cacheId] as $n => $entry)
            gettype($data['args'][$n]) !== $entry['type']
                && !(gettype($data['args'][$n]) === 'NULL' && ($entry['type'] === 'object' || $entry['type'] === 'array'))
                    && ($overwrites[$entry['argument']] = $this->convertValue($data['args'][$n], $entry['type']));

        return $overwrites;
    }

    public function getStatus()
    {
        $ret = '';
        $c = count($this->signatureCache);

        if ($c === 0) {
            $ret .= 'No signatures managed by this instance.' . "\n";
        } else {
            if ($c === 1) {
                $ret .= 'Managing 1 signature:' . "\n";
            } else {
                $ret .= 'Managing ' . $c . ' signatures:' . "\n";
            }

            $n = 1;

            foreach ($this->signatureCache as $methodId => $elem) {
                $ret .= '  (' . $n . ') ' . str_replace('.', '::', $methodId) . "\n";
                foreach ($elem as $entry) {
                    $ret .= '        <' . $entry['type'] . '> ' . $entry['argument'] . "\n";
                }

                $n++;
            }
        }

        return $ret;
    }

    /**
     *
     * @param mixed $value
     * @param string $targetType
     * @return array Value in target data type.
     */
    protected function convertValue($value, $targetType)
    {
        if ($targetType === 'boolean') {
            return (bool) $value;
        } elseif ($targetType === 'array') {
            return (array) $value;
        } elseif ($targetType === 'object') {
            return (object) $value;
        }

        $sourceType = gettype($value);

        if ($targetType === 'integer') {
            if ($sourceType === 'array' || $sourceType === 'object' || $sourceType === 'resource') {
                return (int) (bool) $value;
            }

            return (int) $value;
        }

        if ($targetType === 'double') {
            if ($sourceType === 'array' || $sourceType === 'object' || $sourceType === 'resource') {
                return (float) (bool) $value;
            }

            return (float) $value;
        }

        if ($targetType === 'string') {
            if ($sourceType === 'object' && !method_exists($value, '__toString')) {
                return (string) (bool) $value;
            }

            return (string) $value;
        }

        if ($sourceType === 'double') {
            $sourceType = 'float';
        }

        if ($targetType === 'double') {
            $targetType = 'float';
        }

        throw new TypeConversionException(sprintf(
            'Cannot convert from (%s) to (%s).',
            $sourceType,
            $targetType
        ));
    }
}
