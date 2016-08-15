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

use Cake\View\View;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Core\View\Helper\Traits\HelperTrait;
use Cake\View\Helper\HtmlHelper as CakeHtmlHelper;

/**
 * Class HtmlHelper
 *
 * @package Core\View\Helper
 */
class HtmlHelper extends CakeHtmlHelper
{

    use HelperTrait;

    /**
     * HtmlHelper constructor.
     *
     * @param View $View
     * @param array $config
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
        $this->_configWrite('btnPref', Configure::read('Cms.btnPref'));
        $this->_configWrite('iconPref', Configure::read('Cms.iconPref'));
        $this->_configWrite('classPrefix', Configure::read('Cms.classPrefix'));
        $this->_configWrite('templates.icon', '<i class="{{class}}"{{attrs}}></i>');
    }

    /**
     * Create icon element.
     *
     * @param string $icon
     * @param array $options
     * @return null|string
     */
    public function icon($icon = 'home', array $options = [])
    {
        $iconPref = $this->_configRead('iconPref');
        $_classes = [
            $this->_class(__FUNCTION__),
            $iconPref,
            $iconPref . '-' . $icon,
        ];

        $options = $this->_addClass($options, implode(' ', $_classes));
        $classes = $options['class'];
        unset($options['class']);

        $templater = $this->templater();
        return $templater->format(__FUNCTION__, [
            'class' => $classes,
            'attrs' => $templater->formatAttributes($options),
        ]);
    }

    /**
     * Create an html link.
     *
     * @param string $title
     * @param null|string|array $url
     * @param array $options
     * @return string
     */
    public function link($title, $url = null, array $options = [])
    {
        $options = $this->addClass($options, $this->_class(__FUNCTION__));
        $options = Hash::merge([
            'escapeTitle' => false,
            'clear'       => false,
            'label'       => $title,
        ], $options);

        $isClear = (bool) $options['clear'];
        unset($options['clear']);

        //  Set title in html tag.
        if ($this->_isEscapeTitle($title, $isClear, $options)) {
            $title = $this->tag('span', $title, ['class' => $this->_class(__FUNCTION__) . '-title']);
        }

        $options = $this->_getBtnClass($options);
        $options = $this->_getToolTipAttr($options);

        list($options, $iconOptions) = $this->_createIconAttr($options);
        if (isset($iconOptions['createIcon'])) {
            unset($iconOptions['createIcon']);
            $title = $this->icon($options['icon'], $iconOptions) . PHP_EOL . $title;
            unset($options['icon']);
        }

        if (isset($options['iconInline'])) {
            unset($options['iconInline']);
        }

        unset($options['label']);
        return parent::link($title, $url, $options);
    }

    /**
     * Check if need escape link title.
     *
     * @param string $title
     * @param bool $isClear
     * @param array $options
     * @return bool
     */
    protected function _isEscapeTitle($title, $isClear, array $options = [])
    {
        return $options['escapeTitle'] === false && !empty($title) && !$isClear;
    }
}
