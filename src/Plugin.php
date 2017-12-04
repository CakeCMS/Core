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

namespace Core;

use Cake\Core\App;
use JBZoo\Utils\FS;
use JBZoo\Data\Data;
use JBZoo\Utils\Arr;
use Cake\Core\Configure;
use Core\Migration\Manager;
use Cake\Core\Plugin as CakePlugin;

/**
 * Class Plugin
 *
 * @package Core
 */
class Plugin extends CakePlugin
{

    /**
     * Plugin bootstrap file.
     */
    const FILE_BOOTSTRAP = 'bootstrap.php';

    /**
     * Plugin routes file.
     */
    const FILE_ROUTES = 'routes.php';

    /**
     * The plugin manifest file name.
     */
    const PLUGIN_MANIFEST = 'plugin.manifest.php';

    /**
     * Hold plugin data.
     *
     * @var array
     */
    protected static $_data = [];

    /**
     * Holds a list of all plugin events from manifest file.
     *
     * @var array
     */
    protected static $_eventList = [];

    /**
     * Manifest callback list.
     *
     * @var array
     */
    protected static $_manifestEvents = [
        'Controller.initialize',
        'Controller.beforeRender',
        'Controller.beforeFilter',
        'Controller.beforeRedirect',
        'Controller.afterFilter',
        'View.initialize',
        'View.beforeRenderFile',
        'View.afterRenderFile',
        'View.beforeRender',
        'View.afterRender',
        'View.beforeLayout',
    ];

    /**
     * Get plugin manifest data.
     *
     * @param string $plugin
     * @param null|string $key
     * @return Data
     */
    public static function getData($plugin, $key = null)
    {
        $data = self::_checkData($plugin);
        if (empty($data) && $path = self::getManifestPath($plugin)) {
            if (FS::isFile($path)) {
                /** @noinspection PhpIncludeInspection */
                $plgData = include $path;
                $plgData = (array) $plgData;
                if (!empty($plgData)) {
                    self::$_data[$plugin] = $plgData;
                    $data = $plgData;
                }
            }
        }

        return self::_getPluginData($data, $key);
    }

    /**
     * Get plugin locale path.
     *
     * @param string $plugin
     * @return string
     */
    public static function getLocalePath($plugin)
    {
        return self::path($plugin) . 'src' . DS . 'Locale' . DS;
    }

    /**
     * Get absolute plugin manifest file path.
     *
     * @param string $plugin
     * @return null|string
     */
    public static function getManifestPath($plugin)
    {
        if (self::loaded($plugin)) {
            return FS::clean(self::path($plugin) . DS . self::PLUGIN_MANIFEST);
        }

        return null;
    }

    /**
     * Check plugin need migration.
     *
     * @param string $plugin
     * @return bool
     */
    public static function hasMigration($plugin)
    {
        $manager = new Manager($plugin);
        $migrations = array_keys($manager->getMigrations());
        if (count($migrations)) {
            foreach ($migrations as $version) {
                if (!$manager->isMigrated($version)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Load the plugin.
     *
     * @param array|string $plugin
     * @param array $config
     */
    public static function load($plugin, array $config = [])
    {
        parent::load($plugin, $config);

        $plugin = (array) $plugin;
        foreach ($plugin as $name) {
            if ((bool) self::loaded($name)) {
                self::_addManifestCallback($name);
                Cms::mergeConfig('App.paths.locales', self::getLocalePath($name));
            }
        }
    }

    /**
     * Load list plugin.
     *
     * @param array $plugins
     * @return void
     */
    public static function loadList(array $plugins)
    {
        foreach ($plugins as $name) {
            if (self::loaded($name)) {
                continue;
            }

            if ($path = self::_findPlugin($name)) {
                self::load($name, self::_getConfigForLoad($path));
            }
        }
    }

    /**
     * Call plugin manifest callbacks.
     *
     * @return void
     */
    public static function manifestEvent()
    {
        $args     = func_get_args();
        $callback = array_shift($args);

        if (Arr::key($callback, self::$_eventList)) {
            $callbacks = self::$_eventList[$callback];
            foreach ($callbacks as $method) {
                call_user_func_array($method, $args);
            }
        }
    }

    /**
     * Unload the plugin.
     *
     * @param null|string $plugin
     * @return void
     */
    public static function unload($plugin = null)
    {
        if (self::loaded($plugin)) {
            $locales = Configure::read('App.paths.locales');
            foreach ($locales as $key => $path) {
                if ($path == self::getLocalePath($plugin)) {
                    unset($locales[$key]);
                }
            }

            Configure::write('App.paths.locales', $locales);
        }

        parent::unload($plugin);
    }

    /**
     * Registration plugin manifest callbacks.
     *
     * @param string $plugin
     * @return void
     */
    protected static function _addManifestCallback($plugin)
    {
        $data = Plugin::getData($plugin);
        foreach ($data as $name => $callback) {
            if (self::_isCallablePluginData($name, $plugin, $callback)) {
                self::$_eventList[$name][$plugin] = $callback;
            }
        }
    }

    /**
     * Check plugin data.
     *
     * @param string $plugin
     * @return array
     */
    protected static function _checkData($plugin)
    {
        return (Arr::in($plugin, self::$_data)) ? self::$_data[$plugin] : [];
    }

    /**
     * Find plugin dir in registered paths.
     *
     * @param string $name
     * @return null|string
     */
    protected static function _findPlugin($name)
    {
        $output = null;
        $paths  = App::path('Plugin');
        $plugin = Configure::read('plugins.' . $name);

        if ($plugin !== null) {
            return $plugin;
        }

        foreach ($paths as $path) {
            $plgPath = $path . $name . DS;
            if (FS::isDir($plgPath)) {
                $output = $plgPath;
                break;
            }
        }

        return $output;
    }

    /**
     * Get plugin configuration for load plugin.
     *
     * @param string $path
     * @return array
     */
    protected static function _getConfigForLoad($path)
    {
        $config    = ['autoload' => true];
        $routes    = $path . 'config' . DS . Plugin::FILE_ROUTES;
        $bootstrap = $path . 'config' . DS . Plugin::FILE_BOOTSTRAP;

        if (FS::isFile($bootstrap)) {
            $config['bootstrap'] = true;
        }

        if (FS::isFile($routes)) {
            $config['routes'] = true;
        }

        $config['path'] = $path;

        return $config;
    }

    /**
     * Get current plugin data.
     *
     * @param array $data
     * @param null|string $key
     * @return Data
     */
    protected static function _getPluginData(array $data, $key = null)
    {
        if (isset($data[$key])) {
            $data = $data[$key];
        }

        return new Data($data);
    }

    /**
     * Check manifest param on callable.
     *
     * @param string $name
     * @param string $plugin
     * @param mixed $callback
     * @return bool
     */
    protected static function _isCallablePluginData($name, $plugin, $callback)
    {
        if (Arr::in($name, self::$_manifestEvents) &&
            !isset(self::$_eventList[$name][$plugin]) &&
            is_callable($callback)
        ) {
            return true;
        }

        return false;
    }
}
