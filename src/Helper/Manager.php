<?php
/**
 * CakeCMS Core
 *
 * This file is part of the of the simple cms based on CakePHP 3.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @license     MIT
 * @copyright   MIT License http://www.opensource.org/licenses/mit-license.php
 * @link        https://github.com/CakeCMS/Core".
 * @author      Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

namespace Core\Helper;

use Cake\Core\Configure;
use Core\Plugin;
use Core\Container;
use JBZoo\Utils\Arr;
use JBZoo\Utils\Str;
use Cake\Utility\Inflector;

/**
 * Class Manager
 *
 * @package Core\Helper
 */
class Manager extends Container
{

    /**
     * Helper class name suffix.
     */
    const HELPER_SUFFIX = 'Helper';

    /**
     * Hold loaded helpers.
     *
     * @var array
     */
    protected static $_loaded = [];

    /**
     * List of namespaces.
     *
     * @var array
     */
    protected static $_namespace = [];

    /**
     * Manager constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);
        foreach ((array) Plugin::loaded() as $plugin) {
            $this->addNamespace($plugin);
        }
    }

    /**
     * Add new helper group namespace.
     *
     * @param string $name
     * @return bool
     */
    public function addNamespace($name)
    {
        if (!Arr::in($name, self::$_namespace)) {
            self::$_namespace[] = $name;
            return true;
        }

        return false;
    }

    /**
     * Gets a parameter or an object.
     *
     * @param string $id
     * @return mixed
     */
    public function offsetGet($id)
    {
        $id = Str::low($id);
        if (!Arr::key($id, self::$_loaded)) {
            $className = $this->_getClassName($id);
            $this->_register($id, $className);
        }

        return parent::offsetGet($id);
    }

    /**
     * Register helper class.
     *
     * @param string $id
     * @param $className
     */
    protected function _register($id, $className)
    {
        $id = (string) $id;
        if (class_exists($className)) {
            self::$_loaded[$id] = $className;
            $this[$id] = function () use ($className) {
                return new $className();
            };
        } else {
            throw new Exception("Helper \"{{$className}}\" not found!");
        }
    }

    /**
     * Get current helper class name.
     *
     * @param string $class
     * @return string
     */
    protected function _getClassName($class)
    {
        $class = Str::low($class);
        list ($plugin, $className) = pluginSplit($class);
        $return = self::HELPER_SUFFIX . '\\' . Inflector::camelize($className) . self::HELPER_SUFFIX;
        if ($plugin !== null) {
            return Inflector::camelize($plugin) . '\\' . $return;
        }

        return Configure::read('App.namespace') . '\\' . $return;
    }
}
