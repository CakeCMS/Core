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

namespace Core\Utility;

use Cake\Core\App;
use JBZoo\Utils\Slug;
use Cake\Utility\Inflector;
use Core\Toolbar\ToolbarItem;

/**
 * Class Toolbar
 *
 * @package Core\Utility
 */
class Toolbar
{

    const DEFAULT_NAME      = 'toolbar';
    const CLASS_TYPE        = 'Toolbar';
    const CLASS_NAME_PREFIX = 'ToolbarItem';

    /**
     * Button type objects.
     *
     * @var array
     */
    protected $_buttons = [];

    /**
     *  Toolbar array items.
     *
     * @var array
     */
    protected $_items = [];

    /**
     * Toolbar name.
     *
     * @var string
     */
    protected $_name;

    /**
     * Stores the singleton instances of various toolbar.
     *
     * @var array
     */
    protected static $_instances = [];

    /**
     * Toolbar constructor.
     *
     * @param string $name
     */
    public function __construct($name = self::DEFAULT_NAME)
    {
        $this->_name = $name;
    }

    /**
     * Set a value.
     *
     * @return bool
     */
    public function appendButton()
    {
        $btn = func_get_args();
        array_push($this->_items, $btn);

        return true;
    }

    /**
     * Get toolbar instance.
     *
     * @param string $name
     * @return array|Toolbar
     */
    public static function getInstance($name = self::DEFAULT_NAME)
    {
        if (empty(self::$_instances[$name])) {
            self::$_instances[$name] = new Toolbar($name);
        }

        return self::$_instances[$name];
    }

    /**
     * Get toolbar items.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Get toolbar name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Load object of item type.
     *
     * @param $type
     * @return ToolbarItem|bool
     */
    public function loadItemType($type)
    {
        $signature = md5($type);
        if (isset($this->_buttons[$signature])) {
            return $this->_buttons[$signature];
        }

        list($plugin, $name) = pluginSplit($type);
        $alias = Slug::filter($name, '_');
        $aliasClass = Inflector::classify($alias);

        $className = $this->_getItemClassName($plugin, $aliasClass);

        if (!$className) {
            return false;
        }

        $this->_buttons[$signature] = new $className($this);
        return $this->_buttons[$signature];
    }

    /**
     * Set a prepend value.
     *
     * @return bool
     */
    public function prependButton()
    {
        $btn = func_get_args();
        array_unshift($this->_items, $btn);

        return true;
    }

    /**
     * Render toolbar items.
     *
     * @return string
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function render()
    {
        $i = 0;
        $output = [];
        $count  = count($this->_items);
        foreach ($this->_items as $item) {
            $i++;
            $item['class'] = 'item-wrapper tb-item-' . $i;

            if ($i == 1) {
                $item['class'] .= ' first';
            }

            if ($i == $count) {
                $item['class'] .= ' last';
            }

            $output[] = $this->renderItem($item);
        }

        return implode(PHP_EOL, $output);
    }

    /**
     * Render toolbar item html.
     *
     * @param $node
     * @return null|string
     */
    public function renderItem(&$node)
    {
        $type = $node[0];
        $item = $this->loadItemType($type);

        if ($item === false) {
            return null;
        }

        return $item->render($node);
    }

    /**
     * Get full class name.
     *
     * @param string $plugin
     * @param string $aliasClass
     * @return bool|string
     */
    protected function _getItemClassName($plugin, $aliasClass)
    {
        if ($plugin === null) {
            $plugin = 'Core';
        }

        $buttonClass = $plugin . '.' . self::CLASS_NAME_PREFIX . $aliasClass;
        $className   = App::className($buttonClass, self::CLASS_TYPE);

        if ($className === false) {
            $buttonClass = self::CLASS_NAME_PREFIX . $aliasClass;
            $className   = App::className($buttonClass, self::CLASS_TYPE);
        }

        return $className;
    }
}
