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

use Cake\View\View;
use Cake\Event\Event;
use Core\View\AppView;
use Cake\Network\Response;
use Cake\Controller\Controller;

return [
    'meta' => [
        'name'        => 'Test',
        'author'      => 'Cheren',
        'version'     => '0.0.1',
        'copyright'   => 'CakePHP CMS',
        'license'     => 'MIT',
        'email'       => 'kalistratov.s.m@gmail.com',
        'url'         => 'http://cool-code.ru',
        'description' => 'Core plugin for UnionCMS'
    ],
    'View.initialize' => function (AppView $view) {
        $view->set('fromManifest', 'View initialize');
    },
    'Controller.beforeRender' => function (Controller $controller, Event $event) {
        $controller
            ->set('controllerName', $controller->name)
            ->set('eventName', $event->name());
    },
    'Controller.beforeFilter' => function (Controller $controller, Event $event) {
        $controller
            ->set('controllerName', $controller->name)
            ->set('eventName', $event->name());
    },
    'Controller.afterFilter' => function (Controller $controller, Event $event) {
        $controller
            ->set('controllerName', $controller->name)
            ->set('eventName', $event->name());
    },
    'Controller.beforeRedirect' => function (Controller $controller, Event $event, $url, Response $response) {
        $controller
            ->set('controllerName', $controller->name)
            ->set('eventName', $event->name())
            ->set('url', $url)
            ->set('responseType', $response->type());
    },
    'View.beforeRenderFile' => function (View $view, Event $event, $file) {
        $view
            ->set('eventName', $event->name())
            ->set('file', $file);
    },
    'View.beforeRender' => function (View $view, Event $event, $file) {
        $view
            ->set('eventName', $event->name())
            ->set('file', $file);
    },
    'View.beforeLayout' => function (View $view, Event $event, $file) {
        $view
            ->set('eventName', $event->name())
            ->set('file', $file);
    },
    'View.afterRender' => function (View $view, Event $event, $file) {
        $view
            ->set('eventName', $event->name())
            ->set('file', $file);
    },
    'View.afterRenderFile' => function (View $view, Event $event, $file, $content) {
        $view
            ->set('eventName', $event->name())
            ->set('file', $file)
            ->set('content', $content);
    },
    'custom' => [
        'key' => 'value'
    ]
];
