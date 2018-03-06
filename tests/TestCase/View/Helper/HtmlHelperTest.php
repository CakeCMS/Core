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

use Core\Plugin;
use JBZoo\Utils\FS;
use Core\View\AppView;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Core\ORM\Entity\Entity;
use Cake\Http\ServerRequest;
use Core\View\Helper\HtmlHelper;

/**
 * Class HtmlHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 * @method \Core\View\Helper\HtmlHelper _helper()
 */
class HtmlHelperTest extends HelperTestCase
{

    protected $_name = 'Html';
    protected $_plugin = 'Core';

    public function setUp()
    {
        parent::setUp();
        Plugin::load('Test', ['autoload' => true, 'routes' => true]);
        Plugin::routes('Test');
    }

    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload('Test');
    }

    public function testClassName()
    {
        self::assertInstanceOf('Core\View\Helper\HtmlHelper', $this->_helper());
        self::assertFalse($this->_helper()->getConfig('materializeCss'));
    }

    public function testIcon()
    {
        $expected = ['i' => ['class' => 'ck-icon fa fa-home']];
        $this->assertHtml($expected, $this->_helper()->icon());

        $expected = ['i' => ['class' => 'ck-icon fa fa-profile']];
        $this->assertHtml($expected, $this->_helper()->icon('profile'));

        $expected = ['i' => ['class' => 'my-class ck-icon fa fa-profile']];
        $this->assertHtml($expected, $this->_helper()->icon('profile', ['class' => 'my-class']));

        $expected = ['i' => ['class' => 'ck-icon fa fa-profile', 'id' => 'icon', 'data-rel' => 'top']];
        $this->assertHtml($expected, $this->_helper()->icon('profile', ['id' => 'icon', 'data-rel' => 'top']));
    }

    public function testCss()
    {
        $actual = $this->_helper()->css('styles.css');
        $this->assertHtml(['link' => ['rel' => 'stylesheet', 'href' => 'http://localhost/css/styles.css']], $actual);

        $actual = $this->_helper()->css([
            'styles.css',
            'styles.css',
        ]);

        $this->assertHtml(['link' => ['rel' => 'stylesheet', 'href' => 'http://localhost/css/styles.css']], $actual);

        self::assertNull($this->_helper()->css('no-exist.css'));

        $googleJQuery = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js';
        $actual = $this->_helper()->css($googleJQuery);
        $this->assertHtml(['link' => ['rel' => 'stylesheet', 'href' => $googleJQuery]], $actual);

        $actual = $this->_helper()->css([], ['block' => __METHOD__]);
        self::assertNull($actual);

        $actual = $this->_helper()->css('styles.css', ['rel' => 'import']);
        self::assertSame('<style>@import url(http://localhost/css/styles.css);</style>', $actual);

        $this->_helper()->css('styles.css', ['block' => true]);
        $this->assertHtml(
            ['link' => ['rel' => 'stylesheet', 'href' => 'http://localhost/css/styles.css']],
            $this->View->fetch('css')
        );

        $actual = $this->_helper()->css('Test.styles.css');
        $this->assertHtml(
            ['link' => ['rel' => 'stylesheet', 'href' => 'http://localhost/test/css/styles.css']],
            $actual
        );
    }

    public function testLink()
    {
        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link', 'title' => 'My link 1'],
                'span' => ['class' => 'ck-link-title'],
                    'My link 1',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link 1', '#'));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link', 'title' => 'My clear link'],
                'My clear link',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My clear link', '#', ['clear' => true]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'simple ck-link', 'title' => 'My link 2'],
                'span' => ['class' => 'ck-link-title'],
                    'My link 2',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link 2', '#', ['class' => 'simple']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'simple ck-link', 'title' => 'My clear link'],
                'My clear link',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My clear link', '#', ['clear' => true, 'class' => 'simple']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link', 'title' => 'My link button'],
                'span' => ['class' => 'ck-link-title'],
                    'My link button',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link button', '#', ['button' => true]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link btn btn-success', 'title' => 'My link success button'],
                'span' => ['class' => 'ck-link-title'],
                    'My link success button',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link success button', '#', ['button' => 'success']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link btn btn-danger', 'title' => 'My link danger button'],
                'span' => ['class' => 'ck-link-title'],
                    'My link danger button',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link danger button', '#', ['button' => 'danger']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link btn btn-danger', 'title' => 'My link danger button'],
                'My link danger button',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link danger button', '#', [
            'button' => 'danger', 'clear' => true
        ]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link', 'title' => 'My link icon'],
                'i' => ['class' => 'ck-icon fa fa-home'], '/i',
                'span' => ['class' => 'ck-link-title'],
                    'My link icon',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link icon', '#', ['icon' => 'home']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link', 'title' => 'My link icon'],
                'i' => ['class' => 'ck-icon fa fa-home'], '/i',
                'My link icon',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link icon', '#', ['clear' => true, 'icon' => 'home']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link', 'title' => 'My link icon'],
                'i' => ['class' => 'my-class ck-icon fa fa-profile'], '/i',
                'span' => ['class' => 'ck-link-title'],
                    'My link icon',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link icon', '#', [
            'icon' => 'profile', 'iconClass' => 'my-class'
        ]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link ck-icon fa fa-profile', 'title' => 'My link icon'],
                'span' => ['class' => 'ck-link-title'],
                    'My link icon',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link icon', '#', [
            'icon' => 'profile', 'iconInline' => true,
        ]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link ck-icon fa fa-profile', 'title' => 'My link icon'],
                'My link icon',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link icon', '#', [
            'icon' => 'profile', 'iconInline' => true, 'clear' => true,
        ]));

        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => 'My link tooltip',
                'class' => 'ck-link ck-icon fa fa-profile'
            ],
                'span' => ['class' => 'ck-link-title'],
                    'My link tooltip',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link tooltip', '#', [
            'icon' => 'profile', 'iconInline' => true, 'tooltip' => true,
        ]));

        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'data-placement' => 'top',
                'title' => 'Tooltip my title',
                'class' => 'ck-link'
            ],
                'span' => ['class' => 'ck-link-title'],
                    'My link tooltip',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link tooltip', '#', ['tooltip' => 'Tooltip my title']));

        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'data-placement' => 'bottom',
                'title' => 'Tooltip my title',
                'class' => 'ck-link'
            ],
                'span' => ['class' => 'ck-link-title'],
                    'My link tooltip',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link tooltip', '#', [
            'tooltip'    => 'Tooltip my title',
            'tooltipPos' => 'bottom',
        ]));

        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'data-placement' => 'left',
                'title' => 'Tooltip my title',
                'class' => 'ck-link'
            ],
                'span' => ['class' => 'ck-link-title'],
                    'My link tooltip',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link tooltip', '#', [
            'tooltip'    => 'Tooltip my title',
            'tooltipPos' => 'left',
        ]));

        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'data-placement' => 'right',
                'title' => 'My link tooltip',
                'class' => 'ck-link'
            ],
                'span' => ['class' => 'ck-link-title'],
                    'My link tooltip',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->_helper()->link('My link tooltip', '#', [
            'tooltip'    => true,
            'tooltipPos' => 'right',
        ]));

        $helper = $this->_helper();
        $helper->setConfig('prepareBtnClass', function (HtmlHelper $html, array $options) {
            $options = $html->addClass($options, 'from-callback');
            unset($options['button']);
            return $options;
        });

        $expected = [
            'a' => [
                'href'  => 'http://google.com',
                'class' => 'ck-link from-callback',
                'title' => 'Custom'
            ],
                'span' => ['class' => 'ck-link-title'],
                    'Custom',
                '/span',
            '/a'
        ];

        $this->assertHtml($expected, $helper->link('Custom', 'http://google.com', ['button' => 'red']));

        $helper->setConfig('prepareTooltip', function (HtmlHelper $html, array $options) {
            $options = $html->addClass($options, 'from-callback');
            $options['tooltip'] = 'bottom';
            return $options;
        });

        $expected = [
            'a' => [
                'href'    => 'http://google.com',
                'class'   => 'ck-link from-callback',
                'tooltip' => 'bottom',
                'title'   => 'Custom'
            ],
                'span' => ['class' => 'ck-link-title'],
                    'Custom',
                '/span',
            '/a'
        ];

        $this->assertHtml($expected, $helper->link('Custom', 'http://google.com', ['tooltip' => true]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link ck-icon fa fa-profile', 'title' => 'Set custom title'],
                'My link icon',
            '/a'
        ];
        $this->assertHtml($expected, $helper->link('My link icon', '#', [
            'icon' => 'profile', 'iconInline' => true, 'clear' => true, 'title' => 'Set custom title'
        ]));
    }

    public function testLessByString()
    {
        $this->_helper()->less('styles.less', ['block' => true]);
        $expected = ['link' => ['rel' => 'stylesheet', 'href' => 'preg:/.*cache\/[A-Za-z0-9-]+\.css/']];
        $this->assertHtml($expected, $this->View->fetch('css'));
        $this->_clearCache();
    }

    public function testLessByArray()
    {
        $this->_helper()->less(['styles.less'], ['block' => true]);
        $expected = ['link' => ['rel' => 'stylesheet', 'href' => 'preg:/.*cache\/[A-Za-z0-9-]+\.css/']];
        $this->assertHtml($expected, $this->View->fetch('css'));
        $this->_clearCache();
    }

    public function testScript()
    {
        $actual = $this->_helper()->script('scripts.js');
        $this->assertHtml(['script' => ['src' => 'http://localhost/js/scripts.js'], '/script'], $actual);

        $actual = $this->_helper()->script([
            'styles.js',
            'styles.js',
        ]);

        $this->assertHtml([], $actual);

        $googleJQuery = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js';
        $actual = $this->_helper()->script($googleJQuery);
        $this->assertHtml(['script' => ['src' => $googleJQuery], '/script'], $actual);

        $actual = $this->_helper()->script([], ['block' => __METHOD__]);
        self::assertNull($actual);

        $this->_helper()->script('scripts.js', ['block' => true]);
        $this->assertHtml(
            ['script' => ['src' => 'http://localhost/js/scripts.js'], '/script'],
            $this->View->fetch('script')
        );

        $actual = $this->_helper()->script('Test.scripts.js');
        $this->assertHtml(
            ['script' => ['src' => 'http://localhost/test/js/scripts.js'], '/script'],
            $actual
        );
    }

    public function testStatus()
    {
        $helper = $this->_helper();

        $this->assertHtml(['i' => ['class' => 'ck-red ck-icon fa fa-circle']], $helper->status(0));
        $this->assertHtml(['i' => ['class' => 'ck-green ck-icon fa fa-circle']], $helper->status(1));
        $this->assertHtml(['i' => ['class' => 'ck-green ck-icon fa fa-circle']], $helper->status(true));
        $this->assertHtml(['i' => ['class' => 'ck-red ck-icon fa fa-circle']], $helper->status(false));
        $this->assertHtml(['i' => ['class' => 'ck-red ck-icon fa fa-circle']], $helper->status(null));

        $actual = $helper->status(0, ['plugin' => 'Test', 'controller' => 'Metadata', 'action' => 'toggle', 1]);
        $this->assertHtml([
            'a' => [
                'href'     => 'javascript:void(0);',
                'class'    => 'ck-red ck-link',
                'data-url' => '/test/metadata/toggle/1',
                'title'    => ''
            ],
                'i' => ['class' => 'ck-icon fa fa-circle'], '/i',
            '/a'
        ], $actual);

        $actual = $helper->status(1, ['plugin' => 'Test', 'controller' => 'Metadata', 'action' => 'toggle', 1]);
        $this->assertHtml([
            'a' => [
                'href'     => 'javascript:void(0);',
                'class'    => 'ck-green ck-link',
                'data-url' => '/test/metadata/toggle/1',
                'title'    => ''
            ],
                'i' => ['class' => 'ck-icon fa fa-circle'], '/i',
            '/a'
        ], $actual);
    }

    public function testToggle()
    {
        $request = new ServerRequest([
            'params' => [
                'plugin'     => 'Test',
                'controller' => 'Metadata',
                'action'     => 'toggle',
                'pass'       => [1],
            ]
        ]);

        $entity = new Entity([
            'status' => 1,
            'id'     => 10
        ]);
        $view   = new AppView($request);
        $helper = new HtmlHelper($view);

        $actual = $helper->toggle($entity);
        $expected = [
            'div' => ['class' => 'ck-toggle-wrapper jsToggle'],
                'a' => [
                    'href'     => 'javascript:void(0);',
                    'class'    => 'ck-green ck-link',
                    'data-url' => '/test/metadata/toggle/10/1',
                    'title'    => ''
                ],
                    'i' => ['class' => 'ck-icon fa fa-circle'], '/i',
                '/a',
            '/div'
        ];
        $this->assertHtml($expected, $actual);
    }

    public function testCheckMaterializeCssEnable()
    {
        $helper = new HtmlHelper($this->View, ['materializeCss' => true]);

        self::assertInstanceOf('Core\View\Helper\HtmlHelper', $helper);
        self::assertTrue($helper->getConfig('materializeCss'));

        $this->assertHtml([
            'a' => ['href' => '#', 'class' => 'ck-link waves-effect waves-light btn success', 'title' => 'Title'],
                'span' => ['class' => 'ck-link-title'],
                    'Title',
                '/span',
            '/a'
        ], $helper->link('Title', '#', ['button' => 'success']));
    }

    /**
     * @return void
     */
    protected function _clearCache()
    {
        $path = FS::clean(APP_ROOT . Configure::read('App.webroot') . '/' . Configure::read('App.cssBaseUrl') . 'cache');
        $folder = new Folder($path);
        $folder->delete();
    }
}