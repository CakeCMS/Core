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

use Core\Core\Plugin;
use Cake\Event\Event;
use Core\View\AppView;
use Cake\Http\Response;
use Cake\Controller\Controller;

return [
    'meta' => [
        'name'        => 'Core',
        'author'      => 'Cheren',
        'version'     => '0.0.1',
        'copyright'   => 'CakePHP CMS',
        'license'     => 'MIT',
        'email'       => 'kalistratov.s.m@gmail.com',
        'url'         => 'http://cool-code.ru',
        'description' => 'Core plugin for UnionCMS'
    ],

    /**
     * Initialization hook method.
     */
    'Controller.initialize' => function (Controller $controller) {
        $controller->loadComponent('Csrf');
        $controller->loadComponent('Cookie');
        $controller->loadComponent('Security', [
            'unlockedFields' => ['action']
        ]);
        $controller->loadComponent('Paginator');
        $controller->loadComponent('Core.App');
        $controller->loadComponent('Core.Move');
        $controller->loadComponent('Core.Process');
        $controller->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false
        ]);
        $controller->loadComponent('Flash', [
            'className' => 'Core.Flash',
        ]);

        Plugin::manifestEvent('Controller.initialize', $controller);
    },

    /**
     * Called after the controller action is run, but before the view is rendered. You can use this method
     * to perform logic or set view variables that are required on every request.
     */
    'Controller.beforeRender' => function(Controller $controller, Event $event) {
        Plugin::manifestEvent('Controller.beforeRender', $controller, $event);
    },

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     */
    'Controller.beforeFilter' => function(Controller $controller, Event $event) {
        Plugin::manifestEvent('Controller.beforeFilter', $controller, $event);
    },

    'Controller.beforeRedirect' => function(Controller $controller, Event $event, $url, Response $response) {
        Plugin::manifestEvent('Controller.beforeRedirect', $controller, $event, $url, $response);
    },

    /**
     * The beforeRedirect method is invoked when the controller's redirect method is called but before any
     * further action.
     */
    'Controller.afterFilter' => function(Controller $controller, Event $event) {
        Plugin::manifestEvent('Controller.afterFilter', $controller, $event);
    },

    /**
     * Initialization hook method.
     *
     * Properties like $helpers etc. cannot be initialized statically in your custom
     * view class as they are overwritten by values from controller in constructor.
     * So this method allows you to manipulate them as required after view instance
     * is constructed.
     */
    'View.initialize' => function (AppView $view) {
        $view->loadHelper('Core.Nav');
        $view->loadHelper('Core.Less');
        $view->loadHelper('Core.Assets');
        $view->loadHelper('Core.Document');
        $view->loadHelper('Core.Filter');
        $view->loadHelper('Url', ['className' => 'Core.Url']);
        $view->loadHelper('Html', [
            'materializeCss' => true,
            'className'      => 'Core.Html'
        ]);
        $view->loadHelper('Form', [
            'materializeCss' => true,
            'className'      => 'Core.Form'
        ]);
        $view->loadHelper('Core.Js');
        $view->loadHelper('Text');
        $view->loadHelper('Flash');
        $view->loadHelper('Paginator');

        Plugin::manifestEvent('View.initialize', $view);
    },

    /**
     * Is called after layout rendering is complete. Receives the layout filename as an argument.
     */
    'View.afterLayout' => function(AppView $view, Event $event, $layoutFile) {
        Plugin::manifestEvent('View.afterLayout', $view, $event, $layoutFile);
    },

    /**
     * Is called after the view has been rendered but before layout rendering has started.
     */
    'View.afterRender' => function(AppView $view, Event $event, $viewFile) {
        Plugin::manifestEvent('View.afterRender', $view, $event, $viewFile);
    },

    /**
     * A callback can modify and return $content to change how the rendered content will be displayed in the browser.
     */
    'View.afterRenderFile' => function(AppView $view, Event $event, $viewFile, $content) {
        Plugin::manifestEvent('View.afterRenderFile', $view, $event, $viewFile, $content);
    },

    /**
     * Is called before layout rendering starts. Receives the layout filename as an argument.
     */
    'View.beforeLayout' => function(AppView $view, Event $event, $layoutFile) {
        Plugin::manifestEvent('View.beforeLayout', $view, $event, $layoutFile);
    },

    /**
     * Is called after the controller’s beforeRender method but before the controller renders view and layout.
     * Receives the file being rendered as an argument.
     */
    'View.beforeRender' => function(AppView $view, Event $event, $viewFile) {
        Plugin::manifestEvent('View.beforeLayout', $view, $event, $viewFile);
    },

    /**
     * Is called before each view file is rendered. This includes elements, views, parent views and layouts.
     */
    'View.beforeRenderFile' => function(AppView $view, Event $event, $viewFile) {
        Plugin::manifestEvent('View.beforeRenderFile', $view, $event, $viewFile);
    },

    'events' => [
        'Core.CoreEventHandler',
    ],

    'params' => [
        'Global' => [
            'admin_email' => [
                'type'      => 'email',
                'default'   => 'my@site.net',
                'label'     => __d('core', 'Global email'),
            ]
        ]
    ]
];
