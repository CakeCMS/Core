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

namespace Core\Test\TestCase\View\Helper;

use Core\View\AppView;
use Core\TestSuite\TestCase;
use Core\View\Helper\HtmlHelper;

/**
 * Class HtmlHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 */
class HtmlHelperTest extends TestCase
{

    /**
     * @var HtmlHelper
     */
    protected $Html;

    public function setUp()
    {
        parent::setUp();
        $view = new AppView();
        $this->Html = new HtmlHelper($view);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Html);
    }

    public function testClassName()
    {
        $this->assertInstanceOf('Core\View\Helper\HtmlHelper', $this->Html);
    }

    public function testIcon()
    {
        $expected = ['i' => ['class' => 'ck-icon fa fa-home']];
        $this->assertHtml($expected, $this->Html->icon());

        $expected = ['i' => ['class' => 'ck-icon fa fa-profile']];
        $this->assertHtml($expected, $this->Html->icon('profile'));

        $expected = ['i' => ['class' => 'my-class ck-icon fa fa-profile']];
        $this->assertHtml($expected, $this->Html->icon('profile', ['class' => 'my-class']));

        $expected = ['i' => ['class' => 'ck-icon fa fa-profile', 'id' => 'icon', 'data-rel' => 'top']];
        $this->assertHtml($expected, $this->Html->icon('profile', ['id' => 'icon', 'data-rel' => 'top']));
    }

    public function testLink()
    {
        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link'],
                'span' => ['class' => 'ck-link-title'],
                    'My link 1',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link 1', '#'));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link'],
                'My clear link',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My clear link', '#', ['clear' => true]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'simple ck-link'],
                'span' => ['class' => 'ck-link-title'],
                    'My link 2',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link 2', '#', ['class' => 'simple']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'simple ck-link'],
                'My clear link',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My clear link', '#', ['clear' => true, 'class' => 'simple']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link'],
                'span' => ['class' => 'ck-link-title'],
                    'My link button',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link button', '#', ['button' => true]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link btn btn-success'],
                'span' => ['class' => 'ck-link-title'],
                    'My link success button',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link success button', '#', ['button' => 'success']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link btn btn-danger'],
                'span' => ['class' => 'ck-link-title'],
                    'My link danger button',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link danger button', '#', ['button' => 'danger']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link btn btn-danger'],
                'My link danger button',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link danger button', '#', [
            'button' => 'danger', 'clear' => true
        ]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link'],
                'i' => ['class' => 'ck-icon fa fa-home'], '/i',
                'span' => ['class' => 'ck-link-title'],
                    'My link icon',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link icon', '#', ['icon' => 'home']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link'],
                'i' => ['class' => 'ck-icon fa fa-home'], '/i',
                'My link icon',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link icon', '#', ['clear' => true, 'icon' => 'home']));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link'],
                'i' => ['class' => 'my-class ck-icon fa fa-profile'], '/i',
                'span' => ['class' => 'ck-link-title'],
                    'My link icon',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link icon', '#', [
            'icon' => 'profile', 'iconClass' => 'my-class'
        ]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link ck-icon fa fa-profile'],
                'span' => ['class' => 'ck-link-title'],
                    'My link icon',
                '/span',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link icon', '#', [
            'icon' => 'profile', 'iconInline' => true,
        ]));

        $expected = [
            'a' => ['href' => '#', 'class' => 'ck-link ck-icon fa fa-profile'],
                'My link icon',
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link('My link icon', '#', [
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
        $this->assertHtml($expected, $this->Html->link('My link tooltip', '#', [
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
        $this->assertHtml($expected, $this->Html->link('My link tooltip', '#', ['tooltip' => 'Tooltip my title']));

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
        $this->assertHtml($expected, $this->Html->link('My link tooltip', '#', [
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
        $this->assertHtml($expected, $this->Html->link('My link tooltip', '#', [
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
        $this->assertHtml($expected, $this->Html->link('My link tooltip', '#', [
            'tooltip'    => true,
            'tooltipPos' => 'right',
        ]));

        $this->Html->config('prepareBtnClass', function (HtmlHelper $html, array $options) {
            $options = $html->addClass($options, 'from-callback');
            unset($options['button']);
            return $options;
        });

        $expected = [
            'a' => [
                'href' => 'http://google.com',
                'class' => 'ck-link from-callback'
            ],
                'span' => ['class' => 'ck-link-title'],
                    'Custom',
                '/span',
            '/a'
        ];

        $this->assertHtml($expected, $this->Html->link('Custom', 'http://google.com', ['button' => 'red']));

        $this->Html->config('prepareTooltip', function (HtmlHelper $html, array $options) {
            $options = $html->addClass($options, 'from-callback');
            $options['tooltip'] = 'bottom';
            return $options;
        });

        $expected = [
            'a' => [
                'href' => 'http://google.com',
                'class' => 'ck-link from-callback',
                'tooltip' => 'bottom',
            ],
                'span' => ['class' => 'ck-link-title'],
                    'Custom',
                '/span',
            '/a'
        ];

        $this->assertHtml($expected, $this->Html->link('Custom', 'http://google.com', ['tooltip' => true]));
    }
}