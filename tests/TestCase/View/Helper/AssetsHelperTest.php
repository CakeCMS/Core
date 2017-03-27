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

        $actual = $this->_helper()->getAssets('script.jquery.output');
        $this->assertHtml($expected, $actual);
        self::assertInstanceOf('Core\View\Helper\AssetsHelper', $object);
    }

    public function testJQueryFactory()
    {
        $object   = $this->_helper()->jqueryFactory();
        $expected = [
            'jquery'  => ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            'utils'   => ['script' => ['src' => 'http://localhost/js/libs/utils.min.js']], '/script',
            'factory' => ['script' => ['src' => 'http://localhost/js/libs/jquery-factory.min.js']], '/script',
        ];

        $jquery  = $this->_helper()->getAssets('script.jquery.output');
        $utils   = $this->_helper()->getAssets('script.jquery-utils.output');
        $factory = $this->_helper()->getAssets('script.jquery-factory.output');

        $this->assertHtml($expected['utils'],   $utils);
        $this->assertHtml($expected['jquery'],  $jquery);
        $this->assertHtml($expected['factory'], $factory);

        self::assertInstanceOf('Core\View\Helper\AssetsHelper', $object);
    }

    public function testBootstrap()
    {
        $object = $this->_helper()->bootstrap();
        self::assertInstanceOf('Core\View\Helper\AssetsHelper', $object);
        
        $expected = [
            'jquery'    => ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            'bootstrap' => ['script' => ['src' => 'http://localhost/js/libs/bootstrap.min.js']], '/script',
        ];

        $jquery    = $this->_helper()->getAssets('script.jquery.output');
        $bootstrap = $this->_helper()->getAssets('script.bootstrap.output');

        $this->assertHtml($expected['jquery'], $jquery);
        $this->assertHtml($expected['bootstrap'], $bootstrap);

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/bootstrap.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->_helper()->getAssets('css.bootstrap.output'));
    }

    public function testFancyBox()
    {
        $object = $this->_helper()->fancyBox();
        self::assertInstanceOf('Core\View\Helper\AssetsHelper', $object);

        $expected = [
            'jquery'   => ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            'fancybox' => ['script' => ['src' => 'http://localhost/js/libs/fancybox.min.js']], '/script',
        ];

        $jquery   = $this->_helper()->getAssets('script.jquery.output');
        $fancybox = $this->_helper()->getAssets('script.fancybox.output');

        $this->assertHtml($expected['jquery'], $jquery);
        $this->assertHtml($expected['fancybox'], $fancybox);

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/fancybox.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->_helper()->getAssets('css.fancybox.output'));
    }

    public function testMaterialize()
    {
        $object = $this->_helper()->materialize();
        self::assertInstanceOf('Core\View\Helper\AssetsHelper', $object);

        $expected = [
            'jquery' => ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            'materialize' => ['script' => ['src' => 'http://localhost/js/libs/materialize.min.js']], '/script',
        ];

        $jquery = $this->_helper()->getAssets('script.jquery.output');
        $materialize = $this->_helper()->getAssets('script.materialize.output');

        $this->assertHtml($expected['jquery'], $jquery);
        $this->assertHtml($expected['materialize'], $materialize);

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/materialize.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->_helper()->getAssets('css.materialize.output'));
    }

    public function testSweetAlert()
    {
        $object = $this->_helper()->sweetAlert();
        self::assertInstanceOf('Core\View\Helper\AssetsHelper', $object);

        $expected = [
            'jquery' => ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            'sweetalert' => ['script' => ['src' => 'http://localhost/js/libs/sweetalert.min.js']], '/script',
        ];

        $jquery = $this->_helper()->getAssets('script.jquery.output');
        $sweetalert = $this->_helper()->getAssets('script.sweetalert.output');

        $this->assertHtml($expected['jquery'], $jquery);
        $this->assertHtml($expected['sweetalert'], $sweetalert);

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/sweetalert.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->_helper()->getAssets('css.sweetalert.output'));
    }

    public function testFontAwesome()
    {
        $object = $this->_helper()->fontAwesome();
        self::assertInstanceOf('Core\View\Helper\AssetsHelper', $object);

        $expected = [
            'link' => [
                'rel'  => 'stylesheet',
                'href' => 'http://localhost/css/libs/font-awesome.min.css',
            ]
        ];

        $this->assertHtml($expected, $this->_helper()->getAssets('css.font-awesome.output'));
    }

    public function testGetAssets()
    {
        $this->_helper()->fontAwesome()->materialize();
        $scripts = $this->_helper()->getAssets('script');

        self::assertTrue(is_array($scripts));
        self::assertArrayHasKey('jquery', $scripts);
        self::assertArrayHasKey('materialize', $scripts);
    }

    public function testTableCheckAll()
    {
        $this->_helper()->tableCheckAll();
        $expected = [
            'jquery' => ['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script',
            'tablecheckall' => ['script' => ['src' => 'http://localhost/js/libs/jquery-check-all.min.js']], '/script',
        ];

        $jquery = $this->_helper()->getAssets('script.jquery.output');
        $tableCheckAll = $this->_helper()->getAssets('script.tablecheckall.output');

        $this->assertHtml($expected['jquery'], $jquery);
        $this->assertHtml($expected['tablecheckall'], $tableCheckAll);
    }
}