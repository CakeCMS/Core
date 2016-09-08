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

namespace Core\View;

use Core\Plugin;
use Cake\View\View;
use JBZoo\Utils\FS;

/**
 * Class AppView
 *
 * @package Core\View
 * @property \Core\View\Helper\AssetsHelper $Assets
 * @property \Core\View\Helper\DocumentHelper $Document
 * @property \Core\View\Helper\FormHelper $Form
 * @property \Core\View\Helper\UrlHelper $Url
 * @property \Core\View\Helper\LessHelper $Less
 * @property \Core\View\Helper\HtmlHelper $Html
 */
class AppView extends View
{

    /**
     * Initialization hook method.
     *
     * Properties like $helpers etc. cannot be initialized statically in your custom
     * view class as they are overwritten by values from controller in constructor.
     * So this method allows you to manipulate them as required after view instance
     * is constructed.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        Plugin::manifestEvent('View.initialize', $this);
    }

    /**
     * Render layout partial.
     *
     * @param string $name
     * @param array $data
     * @return null|string
     */
    public function partial($name, array $data = [])
    {
        $file = $this->_getLayoutPartialPath($name);

        if (FS::isFile($file)) {
            return $this->_render($file, $data);
        }

        return null;
    }

    /**
     * Finds an partial filename, returns false on failure.
     *
     * @param string $name
     * @return bool|string
     */
    protected function _getLayoutPartialPath($name)
    {
        list($plugin, $name) = $this->pluginSplit($name);

        $paths       = $this->_paths($plugin);
        $layoutPaths = $this->_getSubPaths('Layout' . DS . 'Partial');

        foreach ($paths as $path) {
            foreach ($layoutPaths as $layoutPath) {
                $partial = $path . $layoutPath . DS . $name . $this->_ext;
                if (FS::isFile($partial)) {
                    return $partial;
                }
            }
        }

        return false;
    }
}
