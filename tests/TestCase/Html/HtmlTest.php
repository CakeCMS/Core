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

namespace Core\Test\TestCase\Html;

use Core\Html\Html;
use Core\TestSuite\TestCase;

/**
 * Class HtmlTest
 *
 * @package Core\Test\TestCase\Html
 */
class HtmlTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        Html::clean();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadMethodNotFound()
    {
        Html::_('no.exists');
    }

    public function testLoadCallableClassMethod()
    {
        Html::addNameSpace('Test\App\Html');
        $this->assertTrue(in_array('Test\App\Html',  Html::getNameSpaces()));

        $result = Html::_('application.getFunctionName');
        $this->assertSame($result, 'getFunctionName');
    }

    public function testCallLoadedMethod()
    {
        $result = Html::register(__FUNCTION__, function () {
            return 'is function';
        });
        $this->assertTrue($result);

        $result = Html::_(__FUNCTION__);
        $this->assertSame('is function', $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadNoExistMethod()
    {
        Html::addNameSpace('Test\App\Html');
        $this->assertTrue(in_array('Test\App\Html',  Html::getNameSpaces()));
        Html::_('application.noExistsMethod');
    }

    public function testAddNameSpaces()
    {
        $list = Html::getNameSpaces();
        $this->assertIsEmptyArray($list);

        Html::addNameSpace('Theme\\Html');
        $list = Html::getNameSpaces();
        $this->assertTrue(in_array('Theme\\Html', $list));
    }

    public function testRegisterFail()
    {
        $this->assertFalse(Html::register(__FUNCTION__, 'function'));
    }

    public function testRegisterSuccess()
    {
        $result = Html::register(__FUNCTION__, function () {
            return 'is function';
        });
        $this->assertTrue($result);
    }

    public function testUnRegisterFail()
    {
        $this->assertFalse(Html::unRegister(__FUNCTION__));
    }

    public function testUnRegisterSuccess()
    {
        $result = Html::register(__FUNCTION__, function () {
            return 'is function';
        });
        $this->assertTrue($result);
        $this->assertTrue(Html::unRegister(__FUNCTION__));
    }
}
