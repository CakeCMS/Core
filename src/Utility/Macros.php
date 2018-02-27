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
     * Replacement data list.
     *
     * @var array|Entity
     */
    protected $_data = [];

    /**
     * Macros constructor.
     *
     * @param   mixed $data
     */
    public function __construct($data = [])
    {
        if ($data instanceof Entity) {
            $data = $data->toArray();
        }

        $this->_data = (array) $data;

        $this->set('base_url', Router::fullBaseUrl());
    }

    /**
     * Get replacement val or all list.
     *
     * @param   null|string|int $key
     * @return  array
     */
    public function get($key = null)
    {
        if (Arr::key($key, $this->_data)) {
            return $this->_data[$key];
        }

        return $this->_data;
    }

    /**
     * Add new value in list.
     *
     * @param   string|int $key
     * @param   string|int $val
     * @return  $this
     */
    public function set($key, $val)
    {
        $this->_data = Hash::merge([$key => $val], $this->_data);
        return $this;
    }

    /**
     * Get replacement text.
     *
     * @param   string $text
     * @return  string mixed
     */
    public function text($text)
    {
        foreach ($this->_data as $macros => $value) {
            $macros = '{' . $macros . '}';
            $text   = preg_replace('#' . $macros . '#ius', $value, $text);
        }

        return $text;
    }
}
