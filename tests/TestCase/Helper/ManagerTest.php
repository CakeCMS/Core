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

namespace Core\Test\TestCase\Helper;

use Core\Helper\Manager;
use Test\Cases\TestCase;
use Core\Helper\Exception;

/**
 * Class ManagerTest
 *
 * @package Core\Test\TestCase\Helper
 */
class ManagerTest extends TestCase
{

    protected $_plugin = 'Test';

    public function testAddNewNameSpace()
    {
        /** @var Manager $hManager */
        $hManager = $this->_cms['helper'];
        self::assertTrue($hManager->addNamespace('test'));
        self::assertFalse($hManager->addNamespace('test'));
    }

    public function testGetHelperObject()
    {
        /** @var Manager $hManager */
        $hManager = $this->_cms['helper'];
        //  Check register plugin app helper.
        self::assertInstanceOf('Test\Helper\AppleHelper', $hManager['test.apple']);
        self::assertInstanceOf('Test\App\Helper\ApplicationHelper', $hManager['application']);
    }

    /**
     * @expectedException Exception
     */
    public function testGetHelperObjectNotFind()
    {
        /** @var Manager $hManager */
        $hManager = $this->_cms['helper'];
        $hManager['test'];
        $hManager['custom'];
    }
}
