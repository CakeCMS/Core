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

use Cake\Cache\Cache;

Cache::config('test_cached', [
    'className' => 'File',
    'duration'  => '+1 week',
    'path'      => CACHE . 'query' . DS . 'cache' . DS,
    'prefix'    => 'cache_',
    'groups'    => ['cached']
]);
