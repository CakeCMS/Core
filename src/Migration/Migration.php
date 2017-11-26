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

namespace Core\Migration;

use Core\Plugin;
use JBZoo\Utils\FS;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;

/**
 * Class Migration
 *
 * @package Core
 */
class Migration
{

    const MIGRATION_DIR = 'Migrations';

    /**
     * Get plugin migration path.
     *
     * @param string $plugin
     * @return string
     */
    public static function getPath($plugin)
    {
        return FS::clean(Plugin::path($plugin) . '/config/' . self::MIGRATION_DIR, '/');
    }

    /**
     * Get data for migration.
     *
     * @param string $plugin
     * @return array
     */
    public static function getData($plugin)
    {
        $data   = [];
        $path   = self::getPath($plugin);
        $dir    = new Folder($path);
        $files  = (array) $dir->find('.*\.php');

        if (count($files) > 0) {
            foreach ($files as $file) {
                $name       = FS::filename($file);
                $segments   = explode('_', $name);
                $version    = array_shift($segments);
                $class      = Inflector::camelize(implode('_', $segments));

                $data[$version] = [
                    'class' => $class,
                    'path'  => $path . DS . $file,
                ];
            }
        }

        return $data;
    }

    /**
     * Get migration manager.
     *
     * @param string $plugin
     * @return Manager
     */
    public static function getManager($plugin)
    {
        return new Manager($plugin);
    }
}
