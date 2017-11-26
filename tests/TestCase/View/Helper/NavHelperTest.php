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

namespace Core\Test\TestCase\View\Helper;

use Core\Nav;
use Core\View\AppView;
use Test\Cases\TestCase;
use Cake\Http\ServerRequest;
use Core\View\Helper\NavHelper;

/**
 * Class NavHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 */
class NavHelperTest extends TestCase
{

    /**
     * @var NavHelper
     */
    protected $Nav;

    /**
     * @var AppView
     */
    protected $View;

    public function setUp()
    {
        parent::setUp();
        $this->View = new AppView();
        $this->Nav  = $this->View->helpers()->load('Core.Nav');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->View, $this->Nav);
    }

    public function testClassName()
    {
        self::assertInstanceOf('Core\View\Helper\NavHelper', $this->Nav);
    }

    public function testRender()
    {
        Nav::add(__METHOD__, __METHOD__, [
            'title' => 'Link 1',
            'weight'=> 1,
            'icon' => 'cog',
            'children' => [
                'child-1' => [
                    'title' => 'Child 1',
                    'url' => '#'
                ],
                'child-2' => [
                    'title' => 'Child 2',
                    'icon' => 'home',
                    'url' => '#'
                ],
            ]
        ]);

        $actual = $this->Nav->render(__METHOD__, Nav::items(__METHOD__));
        $expected = [
            ['ul' => ['class' => 'menu']],
                ['li' => ['class' => 'li-item last first']],
                    ['a' => ['href' => '#', 'title' => 'Link 1', 'class' => 'item-link-1 ck-link']],
                        ['span' => ['class' => 'ck-link-title']],
                            ['i' => ['class' => 'ck-icon fa fa-cog']], '/i',
                            'Link 1',
                        '/span',
                    '/a',
                    'ul' => ['class' => 'child-menu'],
                        ['li' => ['class' => 'li-item first']],
                            ['a' => ['href' => '#', 'title' => 'Child 1', 'class' => 'item-link-1 ck-link']],
                                ['span' => ['class' => 'ck-link-title']],
                                    'Child 1',
                                '/span',
                            '/a',
                        '/li',
                        ['li' => ['class' => 'li-item last']],
                            ['a' => ['href' => '#', 'title' => 'Child 2', 'class' => 'item-link-2 ck-link']],
                                ['span' => ['class' => 'ck-link-title']],
                                    ['i' => ['class' => 'ck-icon fa fa-home']], '/i',
                                    'Child 2',
                                '/span',
                            '/a',
                        '/li',
                    '/ul',
                '/li',
            '/ul'
        ];

        $this->assertHtml($expected, $actual);
        Nav::clear(__METHOD__);
    }

    public function testRenderElementMenuByLevel()
    {
        Nav::add(__METHOD__, __METHOD__, [
            'title' => 'Link 1',
            'weight'=> 1,
            'icon' => 'cog',
            'children' => [
                'child-1' => [
                    'title' => 'Child 1',
                    'url' => '#'
                ],
            ]
        ]);

        $actual = $this->Nav->render(__METHOD__, Nav::items(__METHOD__), [
            'childMenuAttr' => [
                'element' => 'Core.Nav/Level/menu_child'
            ],
        ]);

        $expected = [
            ['ul' => ['class' => 'menu']],
                ['li' => ['class' => 'li-item last first']],
                    ['a' => ['href' => '#', 'title' => 'Link 1', 'class' => 'item-link-1 ck-link']],
                        ['span' => ['class' => 'ck-link-title']],
                            ['i' => ['class' => 'ck-icon fa fa-cog']], '/i',
                            'Link 1',
                        '/span',
                    '/a',
                    'ul' => ['class' => 'child-menu', 'data-test' => 'is-level-2'],
                        ['li' => ['class' => 'li-item last first']],
                            ['a' => ['href' => '#', 'title' => 'Child 1', 'class' => 'item-link-1 ck-link']],
                                ['span' => ['class' => 'ck-link-title']],
                                    'Child 1',
                                '/span',
                            '/a',
                        '/li',
                    '/ul',
                '/li',
            '/ul'
        ];

        $this->assertHtml($expected, $actual);
        Nav::clear(__METHOD__);
    }

    public function testRenderSetCustomElements()
    {
        Nav::add(__METHOD__, __METHOD__, [
            'title' => 'Link 1',
            'weight'=> 1,
            'icon' => 'cog',
            'liClass' => 'customClass',
            'children' => [
                'child-1' => [
                    'title' => 'Child 1',
                    'url' => '#'
                ],
                'child-2' => [
                    'title' => 'Child 2',
                    'icon' => 'home',
                    'url' => '#'
                ],
            ]
        ]);

        $actual = $this->Nav->render(__METHOD__, Nav::items(__METHOD__), [
            'menuAttr' => [
                'id'      => 'my-id',
                'class'   => 'my-test-class',
                'element' => 'Core.Nav/Custom/menu'
            ],
            'childMenuAttr' => [
                'class'   => 'nav-child-class',
                'element' => 'Core.Nav/Custom/menu_child'
            ],
            'itemElement' => 'Core.Nav/Custom/item',
        ]);

        $expected = [
            ['ul' => ['class' => 'my-test-class', 'id' => 'my-id', 'data-test' => 'menu-for-test']],
                ['li' => ['class' => 'li-item customClass last first', 'data-test' => 'current-data-test']],
                    ['a' => ['href' => '#', 'title' => 'Link 1', 'class' => 'item-link-1 ck-link']],
                        ['span' => ['class' => 'ck-link-title']],
                            ['i' => ['class' => 'ck-icon fa fa-cog']], '/i',
                            'Link 1',
                        '/span',
                    '/a',
                    'ul' => ['class' => 'nav-child-class', 'data-test' => 'check-for-test'],
                        ['li' => ['class' => 'li-item first', 'data-test' => 'current-data-test']],
                            ['a' => ['href' => '#', 'title' => 'Child 1', 'class' => 'item-link-1 ck-link']],
                                ['span' => ['class' => 'ck-link-title']],
                                    'Child 1',
                                '/span',
                            '/a',
                        '/li',
                        ['li' => ['class' => 'li-item last', 'data-test' => 'current-data-test']],
                            ['a' => ['href' => '#', 'title' => 'Child 2', 'class' => 'item-link-2 ck-link']],
                                ['span' => ['class' => 'ck-link-title']],
                                    ['i' => ['class' => 'ck-icon fa fa-home']], '/i',
                                    'Child 2',
                                '/span',
                            '/a',
                        '/li',
                    '/ul',
                '/li',
            '/ul'
        ];

        $this->assertHtml($expected, $actual);
        Nav::clear(__METHOD__);
    }

    public function testRenderCallback()
    {
        Nav::add(__METHOD__, __METHOD__, [
            'weight'   => 9,
            'callable' => function (AppView $view) {
                return 'callable partial';
            }
        ]);

        $actual = $this->Nav->render(__METHOD__, Nav::items(__METHOD__));

        $this->assertHtml([
            'ul' => ['class' => 'menu'],
                'callable partial',
            '/ul'
        ], $actual);

        Nav::clear(__METHOD__);
    }

    public function testActiveItemClass()
    {
        $request = new ServerRequest([
            'params' => [
                'prefix'     => 'admin',
                'plugin'     => 'Core',
                'controller' => 'Root',
                'action'     => 'dashboard',
                'pass'       => []
            ]
        ]);

        $view       = new AppView($request);
        /** @var NavHelper $navHelper */
        $navHelper  = $view->helpers()->load('Core.Nav');

        $url = $navHelper->Url->build([
            'prefix' => 'admin',
            'plugin' => 'Core',
            'controller' => 'Root',
            'action' => 'dashboard',
        ]);

        $_SERVER['REQUEST_URI'] = $url;

        Nav::add(__METHOD__, __METHOD__, [
            'title' => 'Link 1',
            'weight'=> 1,
            'icon' => 'cog',
            'children' => [
                'child-1' => [
                    'title' => 'Child 1',
                    'url' => [
                        'prefix' => 'admin',
                        'plugin' => 'Core',
                        'controller' => 'Root',
                        'action' => 'dashboard',
                    ]
                ],
            ]
        ]);

        $actual = $navHelper->render(__METHOD__, Nav::items(__METHOD__));

        $expected = [
            ['ul' => ['class' => 'menu']],
                ['li' => ['class' => 'li-item last first']],
                    ['a' => ['href' => '#', 'title' => 'Link 1', 'class' => 'item-link-1 ck-link']],
                        ['span' => ['class' => 'ck-link-title']],
                            ['i' => ['class' => 'ck-icon fa fa-cog']], '/i',
                            ' Link 1',
                        '/span',
                    '/a',
                    'ul' => ['class' => 'child-menu'],
                        ['li' => ['class' => 'li-item last first active']],
                            ['a' => ['href' => '/admin', 'title' => 'Child 1', 'class' => 'item-link-1 ck-link']],
                                ['span' => ['class' => 'ck-link-title']],
                                    'Child 1',
                                '/span',
                            '/a',
                        '/li',
                    '/ul',
                '/li',
            '/ul'
        ];

        $this->assertHtml($expected, $actual);
    }
}
