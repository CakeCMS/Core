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

namespace Core\Test\TestCase\Utility;

use Core\Plugin;
use Core\Utility\Toolbar;
use Core\TestSuite\TestCase;

/**
 * Class ToolbarTest
 *
 * @package Core\Test\TestCase\Utility
 */
class ToolbarTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        Plugin::load('Test', ['autoload' => true]);
    }

    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload('Test');
    }

    public function testClassName()
    {
        $object = Toolbar::getInstance(__FUNCTION__);
        $this->assertInstanceOf('Core\Utility\Toolbar', $object);
    }

    public function testGetName()
    {
        $object = Toolbar::getInstance(__FUNCTION__);
        $this->assertSame(__FUNCTION__, $object->getName());
    }

    public function testAppendButton()
    {
        $object = Toolbar::getInstance(__FUNCTION__);
        $result = $object->appendButton('test', 'value', 'key');
        $toolbarItems = $object->getItems();

        $this->assertTrue($result);
        $this->assertTrue(is_array($toolbarItems));
        $this->assertSame(3, count($toolbarItems[0]));
    }

    public function testPrependButton()
    {
        $object = Toolbar::getInstance(__FUNCTION__);
        $result = $object->appendButton('append', 'value', 'key');
        $this->assertTrue($result);

        $result = $object->prependButton('prepend', 'value', 'key');
        $this->assertTrue($result);

        $toolbarItems = $object->getItems();
        $this->assertTrue(is_array($toolbarItems));
        $this->assertSame(2, count($toolbarItems));
        
        $this->assertSame([
            0 => [
                0 => 'prepend',
                1 => 'value',
                2 => 'key',
            ],
            1 => [
                0 => 'append',
                1 => 'value',
                2 => 'key',
            ]
        ], $toolbarItems);
    }

    public function testLoadButtonTypeFromPlugin()
    {
        $object = Toolbar::getInstance(__FUNCTION__);
        $result = $object->loadItemType('Test.simple');

        $this->assertInstanceOf('Test\Toolbar\ToolbarItemSimple', $result);
    }

    public function testLoadButtonTypeNotFindClass()
    {
        $object = Toolbar::getInstance(__FUNCTION__);
        $this->assertFalse($object->loadItemType('simple'));
        $this->assertFalse($object->loadItemType('Test.no-exist'));
    }

    public function testLoadButtonTypeFromApp()
    {
        $object = Toolbar::getInstance(__FUNCTION__);
        $result = $object->loadItemType('view');

        $this->assertInstanceOf('Test\App\Toolbar\ToolbarItemView', $result);
    }

    public function testRenderButton()
    {
        $object = Toolbar::getInstance(__FUNCTION__);
        $object->appendButton('Test.simple', 'home', 'value');

        $output = $object->render();
        $this->assertHtml([
            'div' => ['class' => 'btn-wrapper', 'id' => 'test-render-button-test-simple'],
                'Test.simple',
            '/div'
        ], $output);

        $output = $object->render();
        $this->assertHtml([
            'div' => ['class' => 'btn-wrapper', 'id' => 'test-render-button-test-simple'],
                'Test.simple',
            '/div'
        ], $output);
    }

    public function testRenderButtonNoExistRenderer()
    {
        $object = Toolbar::getInstance(__FUNCTION__);
        $object->appendButton('Test.renderer', 'home', 'value');

        $output = $object->render();
        $this->assertEmpty($output);
    }
}
