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
use Cake\Utility\Hash;
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
     * Plugin name.
     *
     * @var string
     */
    protected $_plugin = '';

    /**
     * Helper configure.
     *
     * @var array
     */
    protected $_config = [];

    /**
     * @var \Core\View\AppView
     */
    protected $View;

    public function setUp()
    {
        parent::setUp();
        Plugin::load($this->_plugin, ['path' => ROOT . DS, 'bootstrap' => true]);
        $this->View = new AppView();
    }

    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload($this->_plugin);
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