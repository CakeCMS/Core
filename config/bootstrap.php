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

use Cake\Core\Configure;

/**
 * CMS configuration.
 *
 * - docDir     - html document dir
 * - iconPrefix - icon class prefix (Default is "Awesome Font")
 * - btnPrefix  - button prefix (Default is "Bootstrap Framework)
 */
Configure::write('Cms', [
    'docDir'      => 'ltr',
    'iconPref'    => 'fa',
    'btnPref'     => 'btn',
    'lineTab'     => '    ',
    'classPrefix' => 'ck'
]);

Configure::write('Cache.defaultConfig', [
    'className' => 'File',
    'duration'  => '+1 hour',
    'path'      => CACHE . 'queries' . DS,
    'prefix'    => 'un_',
]);
