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
        $expected = [['script' => ['src' => 'http://localhost/js/libs/jquery.min.js']], '/script'];

        $this->assertHtml($expected, $this->View->fetch('script'));
        $this->assertInstanceOf('Core\View\Helper\AssetsHelper', $object);
    }
}