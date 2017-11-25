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
use Cake\Core\Configure;

/**
 * Class Theme
 *
 * @package Core
 */
class Theme extends Plugin
{

    /**
     * Setup current theme.
     *
     * @param null|string $prefix
     * @return null|string
     */
    public static function setup($prefix = null)
    {
        $theme = self::name($prefix);
        $path  = self::find($theme);

        if ($path !== null) {
            $config = self::getData($theme, 'meta');
            if ($config->get('type') == 'theme') {
                return $theme;
            }
        }

        return null;
    }

    /**
     * Get name by prefix.
     *
     * @param string|null $prefix
     * @return mixed
     */
    public static function name($prefix = null)
    {
        return ($prefix == 'admin') ? Configure::read('Theme.admin') : Configure::read('Theme.site');
    }

    /**
     * Find theme plugin in path.
     *
     * @param string $theme
     * @return null|string
     */
    public static function find($theme)
    {
        $paths = App::path('Plugin');
        foreach ($paths as $path) {
            $path = FS::clean($path . '/', DS);
            $themeFolder = $path . $theme;

            if (FS::isDir($themeFolder)) {
                return $themeFolder;
            }
        }

        return Configure::read('plugins.' . $theme);
    }
}
