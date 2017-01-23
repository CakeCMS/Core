<?php
/**
 * CakeCMS Core
 *
 * This file is part of the of the simple cms based on CakePHP 3.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Core
 * @license   MIT
 * @copyright MIT License http://www.opensource.org/licenses/mit-license.php
 * @link      https://github.com/CakeCMS/Core".
 * @author    Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

namespace Core\Html;

use JBZoo\Utils\Arr;
use JBZoo\Utils\Str;

/**
 * Class Html
 *
 * @package Core\Html
 */
class Html
{

    /**
     * An array to hold namespaces.
     *
     * @var array
     */
    protected static $_namespaces = [];

    /**
     * An array to hold method references.
     *
     * @var array
     */
    protected static $_registry = [];

    /**
     * Class loader method.
     *
     * @throws \InvalidArgumentException
     * @param string $key The name of helper method to load, (prefix).(class).function.
     * @return mixed Result of JHtml::call($function, $args)
     */
    public static function _($key)
    {
        list($key, $prefix, $file, $func) = static::_extract($key);

        if (Arr::key($key, self::$_registry)) {
            $function = static::$_registry[$key];
            $args = func_get_args();
            // Remove function name from arguments
            array_shift($args);
            return static::_call($function, $args);
        }

        $className     = $prefix . ucfirst($file);
        $fullClassName = self::_classExists($className);

        if ($fullClassName === false) {
            throw new \InvalidArgumentException(sprintf('%s not found.', $className));
        }

        $toCall = [$fullClassName, $func];
        if (is_callable($toCall)) {
            static::register($key, $toCall);
            $args = func_get_args();
            // Remove function name from arguments
            array_shift($args);
            return static::_call($toCall, $args);
        } else {
            throw new \InvalidArgumentException(sprintf('%s::%s not found.', $className, $func), 500);
        }
    }

    /**
     * Add namespaces list.
     *
     * @param string $namespace
     * @return void
     */
    public static function addNameSpace($namespace)
    {
        array_push(self::$_namespaces, $namespace);
    }

    /**
     * Clean registered namespaces.
     *
     * @return void
     */
    public static function clean()
    {
        self::$_namespaces = [];
    }

    /**
     * Get namespaces list.
     *
     * @return array
     */
    public static function getNameSpaces()
    {
        return self::$_namespaces;
    }

    /**
     * Registers a function to be called with a specific key.
     *
     * @param string $key
     * @param string $function Function or method
     * @return bool
     */
    public static function register($key, $function)
    {
        list($key) = static::_extract($key);
        if (is_callable($function)) {
            static::$_registry[$key] = $function;
            return true;
        }

        return false;
    }

    /**
     * Removes a key for a method from registry.
     *
     * @param string $key  The name of the key
     * @return boolean True if a set key is unset
     */
    public static function unRegister($key)
    {
        list($key) = static::_extract($key);
        if (Arr::key($key, static::$_registry)) {
            unset(static::$_registry[$key]);
            return true;
        }

        return false;
    }

    /**
     * Function caller method.
     *
     * @param callable $function Function or method to call
     * @param array $args Arguments to be passed to function
     * @return mixed Function result or false on error.
     * @see https://secure.php.net/manual/en/function.call-user-func-array.php
     * @throws \InvalidArgumentException
     */
    protected static function _call($function, $args)
    {
        // PHP 5.3 workaround
        $temp = [];
        foreach ($args as &$arg) {
            $temp[] = &$arg;
        }

        return call_user_func_array($function, $temp);
    }

    /**
     * Check html class exists.
     *
     * @param string $className
     * @return bool
     */
    protected static function _classExists($className)
    {
        $namespaces = self::$_namespaces;
        array_push($namespaces, __NAMESPACE__);
        foreach ($namespaces as $namespace) {
            $className = $namespace . '\\' . $className;
            if (class_exists($className)) {
                return $className;
            }
        }

        return false;
    }

    /**
     * Method to extract a key.
     *
     * @param string $key The name of helper method to load, (prefix).(class).function
     * prefix and class are optional and can be used to load custom html helpers.
     * @return array
     */
    protected static function _extract($key)
    {
        $key = preg_replace('#[^A-Z0-9_\.]#i', '', $key);
        $details = explode('.', $key);

        $prefix = (count($details) === 3 ? array_shift($details) : 'Html');
        $file   = (count($details) === 2 ? array_shift($details) : '');
        $func   = array_shift($details);

        return [Str::low($prefix . '.' . $file . '.' . $func), $prefix, $file, $func];
    }
}
