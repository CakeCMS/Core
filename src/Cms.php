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

use Core\Path\Path;
use JBZoo\Utils\FS;
use Pimple\Container;
use Cake\Utility\Hash;
use Cake\Core\Configure;

/**
 * Class Cms
 *
 * @package Core
 */
class Cms extends Container
{

    /**
     * Get cms instance.
     *
     * @return Cms
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new self();
            $instance->_initialize();
        }

        return $instance;
    }

    /**
     * Merge configure values by key.
     *
     * @param string $key
     * @param array|string $config
     * @return array|mixed
     */
    public static function mergeConfig($key, $config)
    {
        $values = Hash::merge((array) Configure::read($key), $config);
        Configure::write($key, $values);

        return $values;
    }

    /**
     * On initialize application.
     *
     * @return void
     */
    protected function _initialize()
    {
        $this['path'] = function () {
            return $this->_initPaths();
        };
    }

    /**
     * Init base paths.
     *
     * @return Path
     * @throws \JBZoo\Path\Exception
     */
    protected function _initPaths()
    {
        $path = Path::getInstance();
        $path->setRoot(ROOT);

        //  Setup all webroot paths
        $path->set('webroot', Configure::read('App.wwwRoot'));
        foreach ((array) Plugin::loaded() as $name) {
            $plgPath = Plugin::path($name) . '/' . Configure::read('App.webroot') . '/';
            $path->set('webroot', FS::clean($plgPath), Path::MOD_APPEND);
        }

        //  Setup applicatiojn paths
        $paths = Configure::read('App.paths');
        foreach ($paths as $alias => $_paths) {
            $path->set($alias, $_paths);
        }

        return $path;
    }
}
