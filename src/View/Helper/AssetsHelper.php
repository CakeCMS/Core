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

namespace Core\View\Helper;

use JBZoo\Utils\Str;
use Cake\Utility\Hash;

/**
 * Class AssetsHelper
 *
 * @package Core\View\Helper
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Core\View\Helper\HtmlHelper $Html
 */
class AssetsHelper extends AppHelper
{

    /**
     * Use helpers.
     *
     * @var array
     */
    public $helpers = [
        'Url'  => ['className' => 'Core.Url'],
        'Html' => ['className' => 'Core.Html'],
    ];

    /**
     * Default assets options.
     *
     * @var array
     */
    protected $_options = [
        'block'    => 'assets',
        'fullBase' => true,
        'weight'   => 10,
    ];

    /**
     * Get sort assets included list.
     *
     * @param string $type
     * @return array|null
     */
    public function getAssets($type = 'css')
    {
        return $this->Html->getAssets($type);
    }

    /**
     * Include bootstrap.
     *
     * @return $this
     */
    public function bootstrap()
    {
        $this->jquery();

        $this->Html->script('libs/bootstrap.min.js', $this->_setOptions([
            'weight' => 2,
            'alias'  => __FUNCTION__,
        ]));

        $this->Html->css('libs/bootstrap.min.css', $this->_setOptions([
            'weight' => 1,
            'alias'  => __FUNCTION__,
        ]));

        return $this;
    }

    /**
     * Include fancybox.
     *
     * @return $this
     */
    public function fancyBox()
    {
        $this->jquery();

        $this->Html->script('libs/fancybox.min.js', $this->_setOptions([
            'weight' => 2,
            'alias'  => __FUNCTION__,
        ]));

        $this->Html->css('libs/fancybox.min.css', $this->_setOptions([
            'weight' => 1,
            'alias'  => __FUNCTION__,
        ]));

        return $this;
    }

    /**
     * Include font awesome.
     *
     * @return $this
     */
    public function fontAwesome()
    {
        $this->Html->css('libs/font-awesome.min.css', $this->_setOptions([
            'weight' => 1,
            'alias'  => 'font-awesome',
        ]));

        return $this;
    }

    /**
     * Include jquery lib.
     *
     * @return $this
     */
    public function jquery()
    {
        $this->Html->script('libs/jquery.min.js', $this->_setOptions([
            'weight' => 1,
            'alias'  => __FUNCTION__,
        ]));

        return $this;
    }

    /**
     * Include jquery factory.
     *
     * @return $this
     */
    public function jqueryFactory()
    {
        $this->jquery();

        $this->Html->script('libs/utils.min.js', $this->_setOptions([
            'weight' => 2,
            'alias'  => 'jquery-utils',
        ]));

        $this->Html->script('libs/jquery-factory.min.js', $this->_setOptions([
            'weight' => 2,
            'alias'  => 'jquery-factory',
        ]));

        return $this;
    }

    /**
     * Include materialize design.
     *
     * @return $this
     */
    public function materialize()
    {
        $this->jquery();

        $this->Html->script('libs/materialize.min.js', $this->_setOptions([
            'weight' => 2,
            'alias'  => __FUNCTION__,
        ]));

        $this->Html->css('libs/materialize.min.css', $this->_setOptions([
            'weight' => 1,
            'alias'  => __FUNCTION__,
        ]));

        return $this;
    }

    /**
     * Include sweet alert.
     *
     * @return $this
     */
    public function sweetAlert()
    {
        $this->jquery();

        $this->Html->script('libs/sweetalert.min.js', $this->_setOptions([
            'weight' => 2,
            'alias'  => __FUNCTION__,
        ]));

        $this->Html->css('libs/sweetalert.min.css', $this->_setOptions([
            'weight' => 1,
            'alias'  => __FUNCTION__,
        ]));

        return $this;
    }

    /**
     * Include ui kit framework.
     *
     * @return $this
     */
    public function uikit()
    {
        $this->jquery();

        $this->Html->script('libs/uikit.min.js', $this->_setOptions([
            'weight' => 2,
            'alias'  => __FUNCTION__,
        ]));

        $this->Html->css('libs/uikit.min.css', $this->_setOptions([
            'weight' => 1,
            'alias'  => __FUNCTION__,
        ]));

        return $this;
    }

    /**
     * Autoload plugin assets.
     *
     * @return void
     */
    public function loadPluginAssets()
    {
        $plugin = (string) $this->request->param('plugin');
        $prefix = ($this->request->param('prefix')) ? $this->request->param('prefix') . '/' : null;
        $action = (string) $this->request->param('action');

        $controller = (string) $this->request->param('controller');
        $widgetName = Str::slug($controller . '-' . $action) . '.js';
        $cssOptions = ['block' => 'css_bottom', 'fullBase' => true];

        $this->Html->css($plugin . '.' . $prefix . 'styles.css', $cssOptions);
        $this->Html->less($plugin . '.' . $prefix . 'styles.less', $cssOptions);
        $this->Html->script([
            $plugin . '.' . $prefix . 'widget/' . $widgetName,
            $plugin . '.' . $prefix . 'script.js',
        ], ['block' => 'script_bottom', 'fullBase' => true]);
    }

    /**
     * Setup asset options.
     *
     * @param array $options
     * @return array
     */
    protected function _setOptions(array $options = [])
    {
        return Hash::merge($this->_options, $options);
    }
}
