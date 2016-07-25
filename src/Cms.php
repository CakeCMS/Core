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

namespace Core;

use Cake\Utility\Hash;
use Cake\Core\Configure;

/**
 * Class Cms
 *
 * @package Core
 */
class Cms
{

    /**
     * Merge configure values by key.
     *
     * @param string $key
     * @param array|string $config
     * @return array|mixed
     */
    public static function mergeConfig($key, $config)
    {
        $values = Hash::merge((array) Configure::read($key), $config);
        Configure::write($key, $values);

        return $values;
    }
}
