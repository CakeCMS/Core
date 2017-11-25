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
        self::assertNull($this->_helper()->getBuffer());
        self::assertNull($this->_helper()->getBuffer(['block' => 'buffer']));

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

        self::assertSame($expected, $actual);

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

        self::assertSame($expected, $actual);

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

        self::assertSame($expected, $actual);

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
        self::assertSame($expected, $actual);
    }

    public function testWidget()
    {
        $helper = $this->_helper();
        $result = $helper->widget('.selector', 'WidgetName', [
            'option-1' => 'value-1',
        ]);

        self::assertNull($result);
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

        self::assertSame($expected, $actual);

        $helper->widget('.selector', 'WidgetName', [
            'option-1' => 'value-1',
        ]);

        $actual = $this->_getStrArray($helper->getBuffer());
        self::assertSame($expected, $actual);

        $result = $helper->widget('.selector', 'WidgetNameNew', [
            'option-1' => 'value-1',
        ], true);

        $actual = $this->_getStrArray($result);
        $expected = [
            '<script>' .
                "\t" . 'jQuery(function($){$(".selector").WidgetNameNew({"option-1":"value-1"});});' .
            '</script>',
        ];

        self::assertSame($expected, $actual);
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

        /** @var \Cake\TestSuite\Stub\Response $response */
        $response = $this->_controller->response;

        $this->assertResponseOk();
        $this->assertResponseContains('<title>Dashboard</title>');

        $response->getBody()->rewind();
        $output = $response->getBody()->getContents();

        self::assertRegExp('/"(baseUrl|alert|request|query)":"((\\"|[^"])*)"/i', $output);
    }
}
