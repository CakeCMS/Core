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

use Core\Plugin;

/**
 * Class UrlHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 * @method \Core\View\Helper\UrlHelper _helper()
 */
class UrlHelperTest extends HelperTestCase
{

    protected $_name = 'Url';

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
        $this->assertInstanceOf('Core\View\Helper\UrlHelper', $this->_helper());
    }

    public function testAssetPath()
    {
        $this->assertNotFalse($this->_helper()->assetPath('styles.css'));
        $this->assertNotFalse($this->_helper()->assetPath('styles.css', 'css'));

        $this->assertNotFalse($this->_helper()->assetPath('scripts.js'));
        $this->assertNotFalse($this->_helper()->assetPath('scripts.js', 'js'));

        $this->assertNotFalse($this->_helper()->assetPath('error.less'));
        $this->assertNotFalse($this->_helper()->assetPath('error.less', 'less'));

        $this->assertNotFalse($this->_helper()->assetPath('cake.png', 'img'));
        $this->assertNotFalse($this->_helper()->assetPath('cake-big.jpg', 'img'));

        $this->assertFalse($this->_helper()->assetPath('styles.css', 'js'));

        $this->assertNotFalse($this->_helper()->assetPath('Test.custom.less'));
        $this->assertNotFalse($this->_helper()->assetPath('Test.custom.less', 'less'));

        $this->assertNotFalse($this->_helper()->assetPath('Test.cake.png', 'img'));
    }
}
