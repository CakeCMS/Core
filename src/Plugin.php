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

use Cake\Core\App;
use JBZoo\Utils\FS;
use JBZoo\Data\Data;
use JBZoo\Utils\Arr;
use Cake\Core\Configure;
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
     * Load the plugin.
     *
     * @param array|string $plugin
     * @param array $config
     */
    public static function load($plugin, array $config = [])
    {
        parent::load($plugin, $config);
        
        if (self::loaded($plugin)) {
            Cms::mergeConfig('App.paths.locales', self::getLocalePath($plugin));
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
    protected function _getPluginData(array $data, $key = null)
    {
        if (isset($data[$key])) {
            $data = $data[$key];
        }

        return new Data($data);
    }
}
