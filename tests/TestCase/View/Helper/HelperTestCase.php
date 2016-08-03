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

use Core\View\AppView;
use Cake\TestSuite\TestCase;

/**
 * Class HelperTestCase
 *
 * @package Core\Test\TestCase\View\Helper
 */
class HelperTestCase extends TestCase
{

    /**
     * Helper name.
     *
     * @var string
     */
    protected $_name = '';

    /**
     * @var \Core\View\AppView
     */
    protected $View;

    public function setUp()
    {
        parent::setUp();
        $this->View  = new AppView();
        $helperClass = 'Core\View\Helper\\' . $this->_name . 'Helper';
        $this->{$this->_name} = new $helperClass($this->View);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->View, $this->{$this->_name});
    }
}