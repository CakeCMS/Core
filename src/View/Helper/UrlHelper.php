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

namespace Core\View\Helper;

use JBZoo\Utils\FS;
use Core\Core\Plugin;
use Cake\Core\Configure;
use Cake\View\Helper\UrlHelper as CakeUrlHelper;

/**
 * Class UrlHelper
 *
 * @package Core\View\Helper
 */
class UrlHelper extends CakeUrlHelper
{

    /**
     * Get absolute asset path.
     *
     * @param string $source Plugin.path/to/file.css
     * @param null|string $type Assets folder - default is file ext
     * @return bool|string
     */
    public function assetPath($source, $type = null)
    {
        $ext  = FS::ext($source);
        $type = (empty($type)) ? $ext : $type;

        $path = FS::clean(WWW_ROOT . '/' . $source, '/');
        if (FS::isFile($path)) {
            return $path;
        }

        $path = FS::clean(WWW_ROOT . '/' . $type . '/' . $source, '/');
        if (FS::isFile($path)) {
            return $path;
        }

        $path = $this->_findPluginAsset($source, $type);

        return $path;
    }

    /**
     * Find plugin assets by source.
     *
     * @param string $source
     * @param null|string $type
     * @return bool|string
     */
    protected function _findPluginAsset($source, $type = null)
    {
        list($plugin, $path) = pluginSplit($source);
        $plugin = (string) $plugin;
        if (Plugin::isLoaded($plugin)) {
            $plgPath = implode('/', [Plugin::path($plugin), Configure::read('App.webroot'), $type, $path]);
            $plgPath = FS::clean($plgPath, '/');
            if (FS::isFile($plgPath)) {
                return $plgPath;
            }
        }

        return false;
    }
}
