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
 * @property \Core\View\Helper\AssetsHelper $Assets
 */
class AssetsHelperTest extends HelperTestCase
{

    protected $_name = 'Assets';
    
    public function testJQuery()
    {
        $object   = $this->Assets->jquery();
        $expected = [
            ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
        ];

        $this->assertHtml($expected, $this->View->fetch('script'));
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);
    }

    public function testJQueryFactory()
    {
        $object   = $this->Assets->jqueryFactory();
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
        $object = $this->Assets->bootstrap();
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
        $object = $this->Assets->fancyBox();
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
        $object = $this->Assets->materialize();
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
        $object = $this->Assets->sweetAlert();
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
        $object = $this->Assets->uikit();
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
        $object = $this->Assets->fontAwesome();
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