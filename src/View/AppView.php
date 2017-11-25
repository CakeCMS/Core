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

namespace Core\View;

use Core\Cms;
use Core\Plugin;
use Cake\Core\App;
use Cake\View\View;
use JBZoo\Utils\Arr;
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
 * @property \Core\View\Helper\NavHelper $Nav
 * @property \Core\View\Helper\FilterHelper $Filter
 * @property \Core\View\Helper\JsHelper $Js
 */
class AppView extends View
{

    const VIEW_FORM = 'form';

    /**
     * Hold CMS object.
     *
     * @var Cms
     */
    public $cms;

    /**
     * Controller form actions.
     *
     * @var array
     */
    protected $_formActions = ['edit', 'add'];

    /**
     * Get view file path.
     *
     * @param string $name
     * @return null|string
     */
    public function getViewFile($name)
    {
        list($plugin, $name) = $this->pluginSplit($name);
        $return = null;
        foreach ($this->_paths($plugin) as $path) {
            $viewFile = FS::clean($path . $name . $this->_ext);
            if (FS::isFile($viewFile)) {
                $return = $this->_checkFilePath($viewFile, $path);
                break;
            }
        }

        return $return;
    }

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
        $this->cms = Cms::getInstance();
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
     * Renders view for given template file and layout.
     *
     * @param null|string $view
     * @param null|string $layout
     * @return null|string
     */
    public function render($view = null, $layout = null)
    {
        $view = $this->_getFormView($view);
        return parent::render($view, $layout);
    }

    /**
     * Find form view by request.
     *
     * @return string|null
     */
    protected function _findViewByRequest()
    {
        $paths = App::path('Template', $this->plugin);

        $action      = (string) $this->request->getParam('action');
        $controller  = $this->request->getParam('controller');
        $viewFile    = $action . $this->_ext;
        $viewSubPath = $this->_getSubPaths($controller);

        foreach ($paths as $path) {
            foreach ($viewSubPath as $subPath) {
                $full = $path . $subPath . DS . $viewFile;
                if (FS::isFile($full)) {
                    return $action;
                }

                $formView = $path . $subPath . DS . self::VIEW_FORM . $this->_ext;
                if (FS::isFile($formView)) {
                    return self::VIEW_FORM;
                }
            }
        }

        return null;
    }

    /**
     * Get current form view.
     *
     * @param null|string $view
     * @return null
     */
    protected function _getFormView($view = null)
    {
        if (empty($view) && Arr::in($this->request->getParam('action'), $this->_formActions)) {
            $view = $this->_findViewByRequest();
        }

        return $view;
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
