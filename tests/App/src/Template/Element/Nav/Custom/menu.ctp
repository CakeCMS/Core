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
 * @var         array $content
 * @var         array $options
 * @var         \Core\View\AppView $this
 */

unset($options['menuAttr']['element']);

$options['menuAttr']['data-test'] = 'menu-for-test';

echo $this->Html->tag('ul', implode(PHP_EOL, $content), $options['menuAttr']);
