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
 * @var         array $item
 * @var         int $count
 * @var         string $children
 * @var         array $options
 * @var         \Core\View\AppView $this
 */

$title = h($item['title']);

unset($options['itemElement']);

$linkAttr = [
    'title' => $title,
    'class' => 'item-link-' . $count,
];

if (isset($item['linkClass'])) {
    $linkAttr = $this->Html->addClass($linkAttr, $item['linkClass']);
}

if ($icon = $item['icon']) {
    $title = $this->Html->icon($icon) . "\n" . $title;
}

$liContent = $this->Html->link($title, $item['url'], $linkAttr);

if ($children !== false) {
    $liContent .= $children;
}

echo $this->Html->tag('li', $liContent, $this->Nav->getLiAttr());
