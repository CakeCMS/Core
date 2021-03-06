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

use JBZoo\Utils\Str;
use Cake\Utility\Hash;
use Cake\Core\Configure;

/**
 * Class AssetsHelper
 *
 * @package     Core\View\Helper
 * @property    \Core\View\Helper\JsHelper $Js
 * @property    \Cake\View\Helper\UrlHelper $Url
 * @property    \Core\View\Helper\HtmlHelper $Html
 *
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class AssetsHelper extends AppHelper
{

    const WEIGHT_CORE   = 1;
    const WEIGHT_LIB    = 2;
    const WEIGHT_WIDGET = 3;

    /**
     * Use helpers.
     *
     * @var array
     */
    public $helpers = [
        'Core.Js',
        'Url'  => ['className' => 'Core.Url'],
        'Html' => ['className' => 'Core.Html'],
    ];

    /**
     * Default assets options.
     *
     * @var array
     */
    protected $_options = [
        'weight'   => 10,
        'fullBase' => true,
        'block'    => 'assets'
    ];

    /**
     * Include bootstrap.
     *
     * @return  $this
     */
    public function bootstrap()
    {
        $this->jquery();

        $this->Html->script('libs/bootstrap.min.js', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_LIB
        ]));

        $this->Html->css('libs/bootstrap.min.css', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_CORE
        ]));

        return $this;
    }

    /**
     * Include fancybox.
     *
     * @return  $this
     */
    public function fancyBox()
    {
        $this->jquery();

        $this->Html->script('libs/fancybox.min.js', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_LIB
        ]));

        $this->Html->css('libs/fancybox.min.css', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_CORE
        ]));

        return $this;
    }

    /**
     * Include font awesome.
     *
     * @return  $this
     */
    public function fontAwesome()
    {
        $this->Html->css('libs/font-awesome.min.css', $this->_setOptions([
            'alias'  => 'font-awesome',
            'weight' => self::WEIGHT_CORE
        ]));

        return $this;
    }

    /**
     * Get sort assets included list.
     *
     * @param   string $type
     * @return  array|null
     */
    public function getAssets($type = 'css')
    {
        return $this->Html->getAssets($type);
    }

    /**
     * Include jQuery imgAreaSelect plugin. See https://github.com/odyniec/imgareaselect
     *
     * @return  $this
     */
    public function imgAreaSelect()
    {
        $this->jquery();

        $this->Html->script('libs/img-area-select.min.js', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_LIB
        ]));

        return $this;
    }

    /**
     * Include jquery lib.
     *
     * @return  $this
     */
    public function jquery()
    {
        $this->Html->script('libs/jquery.min.js', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_CORE
        ]));

        return $this;
    }

    /**
     * Include jquery factory.
     *
     * @return  $this
     */
    public function jqueryFactory()
    {
        $this->jquery();

        $this->Html->script('libs/utils.min.js', $this->_setOptions([
            'alias'  => 'jquery-utils',
            'weight' => self::WEIGHT_LIB
        ]));

        $this->Html->script('libs/jquery-factory.min.js', $this->_setOptions([
            'weight' => self::WEIGHT_LIB,
            'alias'  => 'jquery-factory'
        ]));

        return $this;
    }

    /**
     * Autoload plugin assets.
     *
     * @return  void
     *
     * @throws  \JBZoo\Less\Exception
     */
    public function loadPluginAssets()
    {
        $request = $this->getView()->getRequest();
        $plugin  = (string) $request->getParam('plugin');
        $prefix  = ($request->getParam('prefix')) ? $request->getParam('prefix') . '/' : null;
        $action  = (string) $request->getParam('action');

        $controller = (string) $request->getParam('controller');
        $widgetName = Str::slug($controller . '-' . $action) . '.js';
        $cssOptions = ['block' => 'css_bottom', 'fullBase' => true, 'force' => Configure::read('debug')];

        $this->Html->css($plugin . '.' . $prefix . 'styles.css', $cssOptions);
        $this->Html->less($plugin . '.' . $prefix . 'styles.less', $cssOptions);
        $this->Html->script([
            $plugin . '.' . $prefix . 'widget/' . $widgetName,
            $plugin . '.' . $prefix . 'script.js'
        ], ['block' => 'script_bottom', 'fullBase' => true]);
    }

    /**
     * Include materialize design.
     *
     * @return  $this
     */
    public function materialize()
    {
        $this->jquery();

        $this->Html->script('libs/materialize.min.js', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_LIB
        ]));

        $this->Html->css('libs/materialize.min.css', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_CORE
        ]));

        return $this;
    }

    /**
     * Include jquery slugify plugin.
     *
     * @return  $this
     */
    public function slugify()
    {
        $this->jquery();

        $this->Html->script('libs/slugify.min.js', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_LIB
        ]));

        return $this;
    }

    /**
     * Include sweet alert.
     *
     * @return  $this
     */
    public function sweetAlert()
    {
        $this->jquery();

        $this->Html->script('libs/sweetalert.min.js', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_LIB
        ]));

        $this->Html->css('libs/sweetalert.min.css', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_CORE
        ]));

        return $this;
    }

    /**
     * Include jquery table check all.
     *
     * @return  $this
     */
    public function tableCheckAll()
    {
        $this->jquery();

        $this->Html->script(['libs/jquery-check-all.min.js'], $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_LIB
        ]));

        return $this;
    }

    /**
     * Include toggle field js widget.
     *
     * @param   string $selector
     * @param   string $widget
     * @param   array $options
     * @return  $this
     */
    public function toggleField($selector = '.jsToggleField', $widget = 'JBZoo.FieldToggle', array $options = [])
    {
        $this->jqueryFactory();
        $request = $this->getView()->getRequest();

        $this->Html->script('Core.admin/widget/field-toggle.js', $this->_setOptions([
            'alias'  => __FUNCTION__,
            'weight' => self::WEIGHT_WIDGET
        ]));

        $options = Hash::merge(['token' => $request->getCookie('csrfToken')], $options);
        $this->Js->widget($selector, $widget, $options);

        return $this;
    }

    /**
     * Setup asset options.
     *
     * @param   array $options
     * @return  array
     */
    protected function _setOptions(array $options = [])
    {
        return Hash::merge($this->_options, $options);
    }
}
