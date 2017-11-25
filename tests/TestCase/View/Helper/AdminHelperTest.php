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

/**
 * Class AdminHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 * @method \Core\View\Helper\AdminHelper _helper()
 */
class AdminHelperTest extends HelperTestCase
{

    protected $_name = 'Admin';
    protected $_plugin = 'Core';

    public function testClassName()
    {
        self::assertInstanceOf('Core\View\Helper\AdminHelper', $this->_helper());
    }
}
