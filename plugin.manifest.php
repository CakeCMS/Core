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

use Core\View\AppView;
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

    'Controller.initialize' => function (Controller $controller) {
        $controller->loadComponent('RequestHandler');
        $controller->loadComponent('Flash');
    },

    'View.initialize' => function (AppView $view) {
        $view->loadHelper('Url');
        $view->loadHelper('Html');
        $view->loadHelper('Form');
        $view->loadHelper('Text');
        $view->loadHelper('Flash');
        $view->loadHelper('Paginator');
    }
];
