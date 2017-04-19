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

namespace Core\Path;

use JBZoo\Utils\Arr;
use JBZoo\Path\Exception;
use JBZoo\Path\Path as JBPAth;

/**
 * Class Path
 *
 * @package Core\Path
 */
class Path extends JBPAth
{

    /**
     * Get path instance.
     *
     * @param string $key
     * @return Path
     * @throws Exception
     */
    public static function getInstance($key = 'default')
    {
        if ((string) $key = '') {
            throw new Exception('Invalid object key');
        }

        if (!Arr::key($key, self::$_objects)) {
            self::$_objects[$key] = new self();
        }

        return self::$_objects[$key];
    }
}
