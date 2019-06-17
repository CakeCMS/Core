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

use Core\Plugin;
use Cake\View\View;
use Cake\Utility\Hash;
use Test\Cases\TestCase;

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
     * Plugin name.
     *
     * @var string
     */
    protected $_plugin = 'Core';

    /**
     * Helper configure.
     *
     * @var array
     */
    protected $_config = [];

    /**
     * @var View
     */
    protected $View;

    /**
     * Setup the test case, backup the static object values so they can be restored.
     * Specifically backs up the contents of Configure and paths in App if they have
     * not already been backed up.
     *
     * @return  void
     */
    public function setUp()
    {
        parent::setUp();
        $this->View = new View();
    }

    /**
     * Clears the state used for requests.
     *
     * @return  void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->View);
    }

    /**
     * Get helper object.
     *
     * @param null|string $name
     * @param array $config
     * @return mixed
     */
    protected function _helper($name = null, array $config = [])
    {
        $name   = ($name !== null) ? $name : $this->_name;
        $object = $this->_plugin . '\View\Helper\\' . $name . 'Helper';
        $config = Hash::merge($config, $this->_config);

        return new $object($this->View, $config);
    }
}