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

/**
 * Class AssetsHelperTest
 * 
 * @package Core\Test\TestCase\View\Helper
 * @method \Core\View\Helper\AssetsHelper _helper()
 */
class AssetsHelperTest extends HelperTestCase
{

    protected $_name = 'Assets';
    protected $_plugin = 'Core';
    
    public function testJQuery()
    {
        $object   = $this->_helper()->jquery();
        $expected = [
            ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
        ];

        $this->assertHtml($expected, $this->View->fetch('script'));
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);
    }

    public function testJQueryFactory()
    {
        $object   = $this->_helper()->jqueryFactory();
        $expected = [
            ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            ['script' => ['src' => 'http://localhost/js/libs/utils.min.js']], '/script',
            ['script' => ['src' => 'http://localhost/js/libs/jquery-factory.min.js']], '/script',
        ];

        $this->assertHtml($expected, $this->View->fetch('script'));
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);
    }

    public function testBootstrap()
    {
        $object = $this->_helper()->bootstrap();
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);
        
        $expected = [
            ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            ['script' => ['src' => 'http://localhost/js/libs/bootstrap.min.js']], '/script',
        ];

        $this->assertHtml($expected, $this->View->fetch('script'));

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/bootstrap.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->View->fetch('css'));
    }

    public function testFancyBox()
    {
        $object = $this->_helper()->fancyBox();
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);

        $expected = [
            ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            ['script' => ['src' => 'http://localhost/js/libs/fancybox.min.js']], '/script',
        ];

        $this->assertHtml($expected, $this->View->fetch('script'));

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/fancybox.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->View->fetch('css'));
    }

    public function testMaterialize()
    {
        $object = $this->_helper()->materialize();
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);

        $expected = [
            ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            ['script' => ['src' => 'http://localhost/js/libs/materialize.min.js']], '/script',
        ];

        $this->assertHtml($expected, $this->View->fetch('script'));

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/materialize.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->View->fetch('css'));
    }

    public function testSweetAlert()
    {
        $object = $this->_helper()->sweetAlert();
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);

        $expected = [
            ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            ['script' => ['src' => 'http://localhost/js/libs/sweetalert.min.js']], '/script',
        ];

        $this->assertHtml($expected, $this->View->fetch('script'));

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/sweetalert.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->View->fetch('css'));
    }

    public function testUIKit()
    {
        $object = $this->_helper()->uikit();
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);

        $expected = [
            ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            ['script' => ['src' => 'http://localhost/js/libs/uikit.min.js']], '/script',
        ];

        $this->assertHtml($expected, $this->View->fetch('script'));

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/uikit.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->View->fetch('css'));
    }

    public function testFontAwesome()
    {
        $object = $this->_helper()->fontAwesome();
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/font-awesome.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->View->fetch('css'));
    }
}