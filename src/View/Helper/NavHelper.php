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
use Cake\Utility\Hash;

/**
 * Class NavHelper
 *
 * @package Core\View\Helper
 * @property \Core\View\Helper\UrlHelper $Url
 */
class NavHelper extends AppHelper
{

    /**
     * List of helpers used by this helper.
     *
     * @var array
     */
    public $helpers = [
        'Url' => ['className' => 'Core.Url']
    ];

    /**
     * Hold item params.
     *
     * @var array
     */
    protected static $_itemParams = [];

    /**
     * Default menu params.
     *
     * @var array
     */
    protected $_default = [
        'menuAttr' => [
            'class'   => 'menu',
            'element' => 'Core.Nav/menu'
        ],
        'childMenuAttr' => [
            'class'   => 'child-menu',
            'element' => 'Core.Nav/menu_child'
        ],
        'itemElement' => 'Core.Nav/item',
    ];

    /**
     * Render menu.
     *
     * @param string $key
     * @param array $items
     * @param array $options
     * @param int $level
     * @return string
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function render($key, array $items = [], array $options = [], $level = 1)
    {
        $i = 0;
        $output    = [];
        $options   = Hash::merge($this->_default, $options);
        $sorted    = Hash::sort($items, '{s}.weight', 'ASC');
        $itemCount = count($sorted);

        foreach ($sorted as $item) {
            $i++;
            $item = $this->_setFirstLast($i, $itemCount, $item);

            $children = false;
            if (count($item['children']) > 0) {
                $children = $this->render($key, $item['children'], $options, $level + 1);
            }

            $itemParams = [
                'options'  => $options,
                'item'     => $item,
                'count'    => $i,
                'children' => $children,
                'level'    => $level,
            ];

            $this->_setItemParams($itemParams);

            if (isset($item['callable']) && is_callable($item['callable'])) {
                $output[] = call_user_func_array($item['callable'], array_merge(['view' => $this->_View], $itemParams));
            } else {
                $output[] = $this->_View->element($options['itemElement'], $itemParams);
            }
        }

        $element = $this->_getCurrentMenuElement($options, $level);

        return $this->_View->element($element, [
            'content' => $output,
            'options' => $options,
        ]);
    }

    /**
     * Get current menu element.
     *
     * @param array $options
     * @param int $level
     * @return string
     */
    protected function _getCurrentMenuElement(array $options = [], $level = 1)
    {
        if ($level > 1) {
            $levelElement = $this->_getLevelElement($options, $level);
            if ($this->_View->elementExists($levelElement)) {
                return $levelElement;
            }

            return $options['childMenuAttr']['element'];
        }

        return $options['menuAttr']['element'];
    }

    /**
     * Get current menu level element.
     *
     * @param array $options
     * @param int $level
     * @return string
     */
    protected function _getLevelElement(array $options, $level)
    {
        $levelElement = 'menu_child_' . $level;
        $element      = $options['childMenuAttr']['element'];

        list($plugin, $path) = $this->_View->pluginSplit($element);
        $path    = FS::clean($path, '/');
        $details = explode('/', $path);
        array_pop($details);

        $path = implode('/', $details);

        return $plugin . '.' . $path . '/' . $levelElement;
    }

    /**
     * Setup first last item params.
     *
     * @param int $i
     * @param int $itemCount
     * @param array $item
     * @return array
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    protected function _setFirstLast($i, $itemCount, array $item = [])
    {
        $item = array_merge(['last' => false, 'first' => false], $item);
        if ($i == 1) {
            $item['first'] = true;
        }

        if ($i == $itemCount) {
            $item['last'] = true;
        }

        return $item;
    }

    /**
     * Get default li attributes.
     *
     * @return array
     */
    public function getLiAttr()
    {
        $params = self::$_itemParams;
        $attr   = ['class' => 'li-item'];

        if (isset($params['item']['liClass'])) {
            $attr = $this->addClass($attr, $params['item']['liClass']);
        }

        if ($params['item']['last']) {
            $attr = $this->addClass($attr, 'last');
        }

        if ($params['item']['first']) {
            $attr = $this->addClass($attr, 'first');
        }

        return $this->_setActive($attr);
    }

    /**
     * Set active item link.
     *
     * @param array $attr
     * @return array
     */
    protected function _setActive(array $attr = [])
    {
        if ($this->Url->build(self::$_itemParams['item']['url']) == env('REQUEST_URI')) {
            $attr = $this->addClass($attr, 'active');
        }

        return $attr;
    }

    /**
     * Set item params fo hold.
     *
     * @param array $params
     * @return void
     */
    protected function _setItemParams(array $params = [])
    {
        self::$_itemParams = $params;
    }
}
