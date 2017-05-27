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

use Cake\ORM\Entity;
use Cake\View\View;
use JBZoo\Utils\Arr;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Core\View\Helper\Traits\HelperTrait;
use Core\View\Helper\Traits\IncludeTrait;
use Cake\View\Helper\HtmlHelper as CakeHtmlHelper;

/**
 * Class HtmlHelper
 *
 * @package Core\View\Helper
 * @property \Core\View\Helper\LessHelper $Less
 * @property \Core\View\Helper\UrlHelper $Url
 * @property \Core\View\Helper\DocumentHelper $Document
 */
class HtmlHelper extends CakeHtmlHelper
{

    use HelperTrait, IncludeTrait;

    /**
     * List of helpers used by this helper.
     *
     * @var array
     */
    public $helpers = [
        'Core.Less',
        'Core.Document',
        'Url' => ['className' => 'Core.Url'],
    ];

    /**
     * Hold assets data.
     *
     * @var array
     */
    protected $_assets = [
        'styles'  => [],
        'scripts' => [],
    ];

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
     * Creates a link element for CSS stylesheets.
     *
     * @param array|string $path
     * @param array $options
     * @return null|string
     */
    public function css($path, array $options = [])
    {
        $options += ['rel' => 'stylesheet'];
        return $this->_include($path, $options, 'css');
    }

    /**
     * Get sort assets included list.
     *
     * @param string $key
     * @return array|null
     */
    public function getAssets($key)
    {
        $value = Hash::get($this->_assets, $key);
        if (is_string($value)) {
            return $value;
        }

        return Hash::sort((array) $value, '{n}.weight', 'asc');
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
     * Creates a CSS stylesheets from less.
     *
     * @param string|array $path
     * @param array $options
     * @return null|string
     */
    public function less($path, array $options = [])
    {
        $cssPath = [];

        if (!Arr::key('force', $options)) {
            $options['force'] = false;
        }

        if (is_array($path)) {
            foreach ($path as $i) {
                if ($result = $this->Less->process($i, $options['force'])) {
                    $cssPath[] = $result;
                }
            }
        }

        if (is_string($path) && $result = $this->Less->process($path, $options['force'])) {
            $cssPath[] = $result;
        }

        return $this->css($cssPath, $options);
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

        $options = $this->_setTitleAttr($title, $options);

        //  Set title in html tag.
        if ($this->_isEscapeTitle($title, $isClear, $options)) {
            $title = $this->tag('span', $title, ['class' => $this->_class(__FUNCTION__) . '-title']);
        }

        $options = $this->_getBtnClass($options);
        $options = $this->_getToolTipAttr($options);

        list($title, $options) = $this->_createIcon($this, $title, $options);
        unset($options['label']);

        return parent::link($title, $url, $options);
    }

    /**
     * Returns one or many `<script>` tags depending on the number of scripts given.
     *
     * @param array|string $path
     * @param array $options
     * @return null|string
     */
    public function script($path, array $options = [])
    {
        return $this->_include($path, $options, 'script');
    }

    /**
     * Create status icon or link.
     *
     * @param int $status
     * @param array $url
     * @param string $icon
     * @return null|string
     */
    public function status($status = 0, array $url = [], $icon = 'circle')
    {
        $class = (int) $status === 1 ? 'ck-green' : 'ck-red';
        if (count($url) === 0) {
            return $this->icon($icon, ['class' => $class]);
        }

        return $this->link(null, 'javascript:void(0);', [
            'icon'     => $icon,
            'class'    => $class,
            'data-url' => $this->Url->build($url)
        ]);
    }

    /**
     * Create and render ajax toggle element.
     *
     * @param Entity $entity
     * @param array $url
     * @param array $data
     * @return string
     */
    public function toggle(Entity $entity, array $url = [], array $data = [])
    {
        if (count($url) === 0) {
            $url = [
                'action'     => 'toggle',
                'prefix'     => $this->request->getParam('prefix'),
                'plugin'     => $this->request->getParam('plugin'),
                'controller' => $this->request->getParam('controller'),
                (int) $entity->get('id'),
                (int) $entity->get('status')
            ];
        }

        $data = Hash::merge([
            'url'    => $url,
            'entity' => $entity,
        ], $data);

        return $this->_View->element('Core.' .  __FUNCTION__, $data);
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

    /**
     * Setup default title attr.
     *
     * @param string $title
     * @param array $options
     * @return array
     */
    protected function _setTitleAttr($title, array $options = [])
    {
        if (!Arr::key('title', $options)) {
            $options['title'] = strip_tags($title);
        }

        return $options;
    }
}
