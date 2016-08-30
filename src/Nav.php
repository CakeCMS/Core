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

namespace Core;
use Cake\Log\Log;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Class Nav
 *
 * @package Core
 */
class Nav
{

    /**
     * Current active menu.
     *
     * @see CroogoNav::activeMenu()
     */
    protected static $_activeMenu = 'sidebar';

    /**
     * Menu items.
     *
     * @var array
     */
    protected static $_items = ['sidebar' => []];

    /**
     * Default params.
     *
     * @var array
     */
    protected static $_defaults = [
        'icon'           => '',
        'title'          => false,
        'url'            => '#',
        'weight'         => 9999,
        'before'         => false,
        'after'          => false,
        'access'         => [],
        'children'       => [],
        'htmlAttributes' => [],
    ];

    /**
     * Getter/setter for activeMenu
     *
     * @param null $menu
     * @return null|string
     */
    public static function activeMenu($menu = null)
    {
        if ($menu === null) {
            $activeMenu = self::$_activeMenu;
        } else {
            $activeMenu = $menu;
        }

        if (!array_key_exists($activeMenu, self::$_items)) {
            self::$_items[$activeMenu] = [];
        }

        self::$_activeMenu = $activeMenu;
        return $activeMenu;
    }

    /**
     * Add a menu item.
     *
     * @param $menu
     * @param $path
     * @param array $options
     */
    public static function add($menu, $path, $options = [])
    {
        // Juggle argument for backward compatibility
        if (is_array($path)) {
            $options = $path;
            $path    = $menu;
            $menu    = self::activeMenu();
        } else {
            self::activeMenu($menu);
        }

        $pathE  = explode('.', $path);
        $pathE  = array_splice($pathE, 0, count($pathE) - 2);
        $parent = join('.', $pathE);

        if (!empty($parent) && !Hash::check(self::$_items[$menu], $parent)) {
            $title = Inflector::humanize(end($pathE));
            $opt = ['title' => $title];
            self::_setupOptions($opt);
            self::add($parent, $opt);
        }

        self::_setupOptions($options);
        $current = Hash::extract(self::$_items[$menu], $path);

        if (!empty($current)) {
            self::_replace(self::$_items[$menu], $path, $options);
        } else {
            self::$_items[$menu] = Hash::insert(self::$_items[$menu], $path, $options);
        }
    }

    /**
     * Clear all menus.
     *
     * @param string $menu
     * @return void
     */
    public static function clear($menu = 'sidebar')
    {
        if ($menu) {
            self::_clear($menu);
        } else {
            self::$_items = [];
        }
    }

    /**
     * Gets default settings for menu items.
     *
     * @return array
     */
    public static function getDefaults()
    {
        return self::$_defaults;
    }

    /**
     * Sets or returns menu data in array.
     *
     * @param string $menu
     * @param null $items
     * @return array
     */
    public static function items($menu = 'sidebar', $items = null)
    {
        if (!is_string($menu)) {
            throw new \UnexpectedValueException('Menu id is not a string');
        }

        if (!empty($items)) {
            self::$_items[$menu] = $items;
        }

        if (!array_key_exists($menu, self::$_items)) {
            Log::error('Invalid menu: ' . $menu);
            return [];
        }

        return self::$_items[$menu];
    }

    /**
     * Get menus.
     *
     * @return array
     */
    public static function menus()
    {
        return array_keys(self::$_items);
    }

    /**
     * Remove a menu item.
     *
     * @param string $path dot separated path in the array.
     * @return void
     */
    public static function remove($path)
    {
        self::$_items = Hash::remove(self::$_items, $path);
    }

    /**
     * Clear menu items.
     *
     * @param $menu
     * @throws \UnexpectedValueException
     */
    protected static function _clear($menu)
    {
        if (array_key_exists($menu, self::$_items)) {
            self::$_items[$menu] = [];
        } else {
            throw new \UnexpectedValueException('Invalid menu: ' . $menu);
        }
    }

    /**
     * Merge $firstArray with $secondArray.
     *
     * Similar to Hash::merge, except duplicates are removed
     * @param array $firstArray
     * @param array $secondArray
     * @return array
     */
    protected static function _merge($firstArray, $secondArray)
    {
        $merged = Hash::merge($firstArray, $secondArray);
        foreach ($merged as $key => $val) {
            if (is_array($val) && is_int(key($val))) {
                $merged[$key] = array_unique($val);
            }
        }
        return $merged;
    }

    /**
     * Replace a menu element.
     *
     * @param array $target pointer to start of array
     * @param string $path path to search for in dot separated format
     * @param array $options data to replace with
     * @return void
     */
    protected static function _replace(&$target, $path, $options)
    {
        $pathE    = explode('.', $path);
        $path     = array_shift($pathE);
        $fragment = join('.', $pathE);

        if (!empty($pathE)) {
            self::_replace($target[$path], $fragment, $options);
        } else {
            $target[$path] = self::_merge($target[$path], $options);
        }
    }

    /**
     * Setup options.
     *
     * @param array $options
     * @return void
     */
    protected static function _setupOptions(&$options)
    {
        $options = self::_merge(self::$_defaults, $options);
        foreach ($options['children'] as &$child) {
            self::_setupOptions($child);
        }
    }
}
