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

namespace Core\Test\TestCase;

use Core\Nav;
use Cake\Utility\Hash;
use Core\TestSuite\TestCase;

/**
 * Class NavTest
 * 
 * @package Core\Test\TestCase
 */
class NavTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        Nav::activeMenu('sidebar');
    }

    public function tearDown()
    {
        parent::tearDown();
        Nav::clear(null);
    }

    public function testNav()
    {
        //  test clear.
        Nav::clear();
        $items = Nav::items();
        $this->assertEquals($items, []);

        //  test first level addition.
        $defaults = Nav::getDefaults();
        $extensions = ['title' => 'Extensions'];
        Nav::add('extensions', $extensions);
        $result = Nav::items();
        $expected = ['extensions' => Hash::merge($defaults, $extensions)];
        $this->assertEquals($result, $expected);

        //  tested nested insertion (1 level).
        $plugins = ['title' => 'Plugins'];
        Nav::add('extensions.children.plugins', $plugins);
        $result = Nav::items();
        $expected['extensions']['children']['plugins'] = Hash::merge($defaults, $plugins);
        $this->assertEquals($result, $expected);

        //  2 levels deep.
        $example = ['title' => 'Example'];
        Nav::add('extensions.children.plugins.children.example', $example);
        $result = Nav::items();
        $expected['extensions']['children']['plugins']['children']['example'] = Hash::merge($defaults, $example);
        $this->assertEquals($result, $expected);
        $this->assertEquals($expected, Nav::items('sidebar'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testNavClearWithException()
    {
        Nav::clear('bogus');
    }

    public function testNavItemsWithBogusMenu()
    {
        $result = Nav::items('bogus');
        $this->assertEquals([], $result);
    }

    public function testNavGetMenus()
    {
        $result = Nav::menus();
        $this->assertEquals(['sidebar'], $result);

        Nav::activeMenu('top');
        Nav::add('foo', ['title' => 'foo']);
        $result = Nav::menus();

        $this->assertEquals(['sidebar', 'top'], $result);
    }

    public function testNavMultipleMenus()
    {
        Nav::activeMenu('top');
        Nav::add('foo', ['title' => 'foo']);

        $menus = array_keys(Nav::items());
        $this->assertFalse(in_array('foo', $menus), 'foo exists in sidebar');

        $menus = array_keys(Nav::items('top'));
        $this->assertTrue(in_array('foo', $menus), 'foo missing in top');
    }

    public function testNavMerge()
    {
        $foo = ['title' => 'foo', 'access' => ['public', 'admin']];
        $bar = ['title' => 'bar', 'access' => ['admin']];

        Nav::clear();
        Nav::add('foo', $foo);
        Nav::add('foo', $bar);

        $items    = Nav::items();
        $expected = ['admin', 'public'];

        sort($expected);
        sort($items['foo']['access']);
        $this->assertEquals($expected, $items['foo']['access']);
    }

    public function testNavAddStringPath()
    {
        $defaults = Nav::getDefaults();
        $options  = ['title' => 'Bar', 'path' => '/my/path'];
        $expected = [
            'test' => [
                'simple' => Hash::merge($defaults, $options)
            ]
        ];

        Nav::add('path', 'test.simple', $options);

        $this->assertSame($expected, Nav::items('path'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testItemsNotString()
    {
        Nav::items([]);
    }

    public function testItemsAddItems()
    {
        $items = ['title' => 'foo', 'access' => ['public', 'admin']];
        Nav::items('simple', $items);

        $this->assertSame($items, Nav::items('simple'));
    }

    public function testRemove()
    {
        $options  = ['title' => 'Bar', 'path' => '/my/path'];
        Nav::add('remove', 'simple', $options);

        $expected = [
            'simple' => Hash::merge(Nav::getDefaults(), $options)
        ];
        $this->assertSame($expected, Nav::items('remove'));

        Nav::remove('remove');
        $this->assertSame([], Nav::items('remove'));
    }
}