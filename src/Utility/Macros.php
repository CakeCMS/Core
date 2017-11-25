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

namespace Core\Utility;

use JBZoo\Utils\Arr;
use Cake\ORM\Entity;
use Cake\Utility\Hash;
use Cake\Routing\Router;

/**
 * Class Macros
 *
 * @package Core\Utility
 */
class Macros
{

    /**
     * Replacement list.
     *
     * @var array
     */
    protected $_list = [];

    /**
     * Macros constructor.
     *
     * @param mixed $list
     */
    public function __construct($list = [])
    {
        if ($list instanceof Entity) {
            /** @var Entity $list */
            $list = $list->toArray();
        }

        $this->_list = (array) $list;
        $this->set('base_url', Router::fullBaseUrl());
    }

    /**
     * Get replacement val or all list.
     *
     * @param null|string|int $key
     * @return array
     */
    public function get($key = null)
    {
        if (Arr::key($key, $this->_list)) {
            return $this->_list[$key];
        }

        return $this->_list;
    }

    /**
     * Add new value in list.
     *
     * @param string|int $key
     * @param string|int $val
     * @return $this
     */
    public function set($key, $val)
    {
        $this->_list = Hash::merge([$key => $val], $this->_list);
        return $this;
    }

    /**
     * Get replacement text.
     *
     * @param string $text
     * @return string mixed
     */
    public function text($text)
    {
        foreach ($this->_list as $macros => $value) {
            $macros = '{' . $macros . '}';
            $text   = preg_replace('#' . $macros . '#ius', $value, $text);
        }

        return $text;
    }
}
