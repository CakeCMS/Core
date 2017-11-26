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

namespace Core\Test\TestCase\Toolbar;

use Test\Cases\TestCase;
use Core\Utility\Toolbar;

/**
 * Class ToolbarItemLinkTest
 *
 * @package Core\Test\TestCase\Toolbar
 */
class ToolbarItemLinkTest extends TestCase
{

    public function testLink()
    {
        $toolbar = Toolbar::getInstance('toolbar-1');
        $toolbar->appendButton('Core.link', 'My title', '#', []);

        $this->assertHtml([
            'div' => ['id' => 'toolbar-1-core-link', 'class' => 'item-wrapper tb-item-1 first last'],
                'a' => ['href' => '#', 'class' => 'ck-link', 'title' => 'My title'],
                    'span' => ['class' => 'ck-link-title'],
                        'My title',
                    '/span',
                '/a',
            '/div'
        ], $toolbar->render());

        $toolbar = Toolbar::getInstance('toolbar-2');
        $toolbar->appendButton('Core.link', 'My title', [
            'prefix'     => 'admin',
            'plugin'     => 'Core',
            'controller' => 'Root',
            'action'     => 'Dashboard',
        ], [
            'class'     => 'custom',
            'icon'      => 'home',
            'iconClass' => 'red'
        ]);

        $this->assertHtml([
            'div' => ['id' => 'toolbar-2-core-link', 'class' => 'item-wrapper tb-item-1 first last'],
                'a' => ['href' => '/admin/Root/Dashboard', 'class' => 'custom ck-link', 'title' => 'My title'],
                    'i' => ['class' => 'red ck-icon fa fa-home'], '/i',
                    'span' => ['class' => 'ck-link-title'],
                        'My title',
                    '/span',
                '/a',
            '/div'
        ], $toolbar->render());
    }
}
