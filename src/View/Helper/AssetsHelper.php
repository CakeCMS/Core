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

/**
 * Class AssetsHelper
 *
 * @package Core\View\Helper
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class AssetsHelper extends AppHelper
{

    /**
     * Use helpers.
     *
     * @var array
     */
    public $helpers = [
        'Html',
    ];

    /**
     * Default assets options.
     *
     * @var array
     */
    protected $_options = [
        'block'    => true,
        'fullBase' => true,
    ];

    /**
     * Include bootstrap.
     *
     * @return $this
     */
    public function bootstrap()
    {
        $this->jquery();
        $this->Html->script('libs/bootstrap.min.js', $this->_options);
        $this->Html->css('libs/bootstrap.min.css', $this->_options);
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
        $this->Html->script('libs/fancybox.min.js', $this->_options);
        $this->Html->css('libs/fancybox.min.css', $this->_options);
        return $this;
    }

    /**
     * Include font awesome.
     *
     * @return $this
     */
    public function fontAwesome()
    {
        $this->Html->css('libs/font-awesome.min.css', $this->_options);
        return $this;
    }

    /**
     * Include jquery lib.
     *
     * @return $this
     */
    public function jquery()
    {
        $this->Html->script('libs/jquery.min.js', $this->_options);
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
        $this->Html->script(['libs/utils.min.js', 'libs/jquery-factory.min.js'], $this->_options);
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
        $this->Html->script('libs/materialize.min.js', $this->_options);
        $this->Html->css('libs/materialize.min.css', $this->_options);
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
        $this->Html->script('libs/sweetalert.min.js', $this->_options);
        $this->Html->css('libs/sweetalert.min.css', $this->_options);
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
        $this->Html->script('libs/uikit.min.js', $this->_options);
        $this->Html->css('libs/uikit.min.css', $this->_options);
        return $this;
    }
}
