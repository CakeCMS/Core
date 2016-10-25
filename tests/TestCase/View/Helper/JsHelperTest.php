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
use Core\TestSuite\IntegrationTestCase;

/**
 * Class JsHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 * @method \Core\View\Helper\JsHelper _helper()
 */
class JsHelperTest extends HelperTestCase
{

    protected $_name = 'Js';

    public function testBufferScript()
    {
        $this->assertNull($this->_helper()->getBuffer());
        $this->assertNull($this->_helper()->getBuffer(['block' => 'buffer']));

        $helper = $this->_helper();
        $helper->setBuffer('alert("Hello world");');

        $actual = $this->_getStrArray($helper->getBuffer());

        $expected = [
            '<script>',
                '//<![CDATA[', '',
                    'jQuery (function($) {',
                        'alert("Hello world");',
                    '});', '',
                '//]]>',
            '</script>',
            '',
        ];

        $this->assertSame($expected, $actual);

        $helper->setBuffer('alert("Hello world 2");');
        $actual = $this->_getStrArray($helper->getBuffer());
        $expected = [
            '<script>',
                '//<![CDATA[', '',
                    'jQuery (function($) {',
                        'alert("Hello world");',
                        'alert("Hello world 2");',
                    '});', '',
                '//]]>',
            '</script>',
            '',
        ];

        $this->assertSame($expected, $actual);

        $helper->setBuffer('alert("On top");', true);
        $actual = $this->_getStrArray($helper->getBuffer());
        $expected = [
            '<script>',
                '//<![CDATA[', '',
                    'jQuery (function($) {',
                        'alert("On top");',
                        'alert("Hello world");',
                        'alert("Hello world 2");',
                    '});', '',
                '//]]>',
            '</script>',
            '',
        ];

        $this->assertSame($expected, $actual);

        $expected = [
            '<script>',
                'jQuery (function($) {',
                    'alert("On top");',
                    'alert("Hello world");',
                    'alert("Hello world 2");',
                '});',
            '</script>',
            '',
        ];

        $actual = $this->_getStrArray($helper->getBuffer(['safe' => false]));
        $this->assertSame($expected, $actual);
    }

    public function testWidget()
    {
        $helper = $this->_helper();
        $result = $helper->widget('.selector', 'WidgetName', [
            'option-1' => 'value-1',
        ]);

        $this->assertNull($result);
        $actual = $this->_getStrArray($helper->getBuffer());
        $expected = [
            '<script>',
                '//<![CDATA[', '',
                    'jQuery (function($) {',
                        '$(".selector").WidgetName({"option-1":"value-1"});',
                    '});', '',
                '//]]>',
            '</script>',
            '',
        ];

        $this->assertSame($expected, $actual);

        $helper->widget('.selector', 'WidgetName', [
            'option-1' => 'value-1',
        ]);

        $actual = $this->_getStrArray($helper->getBuffer());
        $this->assertSame($expected, $actual);

        $result = $helper->widget('.selector', 'WidgetNameNew', [
            'option-1' => 'value-1',
        ], true);

        $actual = $this->_getStrArray($result);
        $expected = [
            '<script>',
                '//<![CDATA[',
                    'jQuery(function($){$(".selector").WidgetNameNew({"option-1":"value-1"});});',
                '//]]>',
            '</script>',
        ];

        $this->assertSame($expected, $actual);
    }
}

/**
 * Class JsHelperTestIntegration
 *
 * @package Core\Test\TestCase\View\Helper
 */
class JsHelperTestIntegration extends IntegrationTestCase
{

    public function testViewScriptVars()
    {
        $this->get($this->_getUrl([
            'prefix'     => 'admin',
            'controller' => 'Root',
            'action'     => 'dashboard',
        ]));

        $this->assertResponseOk();
        $this->assertResponseContains('<title>Dashboard</title>');
        $this->assertRegExp(
            '/"(baseUrl|alert|request|query|data)":"((\\"|[^"])*)"/i',
            $this->_controller->response->body()
        );

        echo $this->_controller->response->body();
    }
}
