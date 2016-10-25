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

namespace Core\TestSuite;

use Core\Plugin;
use JBZoo\Utils\Str;
use Cake\Cache\Cache;
use Cake\TestSuite\TestCase as CakeTestCase;

/**
 * Class TestCase
 *
 * @package Core\TestSuite
 */
class TestCase extends CakeTestCase
{

    /**
     * Default plugin name.
     *
     * @var string
     */
    protected $_plugin = 'Core';

    /**
     * Setup the test case, backup the static object values so they can be restored.
     * Specifically backs up the contents of Configure and paths in App if they have
     * not already been backed up.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $options = [
            'path' => ROOT . DS,
            'bootstrap' => true,
            'routes' => true,
        ];

        if ($this->_plugin !== 'Core') {
            unset($options['path']);
            $options['autoload'] = true;
        }

        Plugin::load($this->_plugin, $options);
        Plugin::routes($this->_plugin);
    }

    /**
     * Clears the state used for requests.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload($this->_plugin);
        Cache::drop('test_cached');
    }

    /**
     * @param string $string
     * @return array
     */
    protected function _getStrArray($string)
    {
        $output  = [];
        $details = explode("\n", $string);
        foreach ($details as $string) {
            $string   = Str::trim($string);
            $output[] = $string;
        }

        return $output;
    }
}
