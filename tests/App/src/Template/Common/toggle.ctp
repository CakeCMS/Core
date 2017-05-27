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
 * @var       \Core\View\AppView $this
 * @var       \Core\ORM\Entity\Entity $entity
 */

$url = [
    'prefix'     => $this->request->getParam('prefix'),
    'plugin'     => $this->request->getParam('plugin'),
    'controller' => $this->request->getParam('controller'),
    'action'     => $this->request->getParam('action'),
    (int) $entity->get('id'),
    (int) $entity->get('status')
];

$output = [
    'id'     => $entity->get('id'),
    'status' => $entity->get('status'),
    'url'    => $this->Url->build($url),
    'output' => $this->Html->status($entity->get('status'), $url)
];

echo json_encode($output);
