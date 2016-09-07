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
use Cake\Utility\Hash;
use Cake\TestSuite\IntegrationTestCase as CakeIntegrationTestCase;

/**
 * Class IntegrationTestCase
 *
 * @package Core\TestSuite
 */
class IntegrationTestCase extends CakeIntegrationTestCase
{

    /**
     * Default plugin.
     *
     * @var string
     */
    protected $_plugin = 'Core';

    /**
     * Default url.
     *
     * @var array
     */
    protected $_url = [
        'prefix' => null,
        'plugin' => 'Core',
        'action' => '',
    ];

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
        Plugin::load('Core', [
            'path'      => ROOT . DS,
            'bootstrap' => true,
            'routes'    => true,
        ]);

        Plugin::routes('Core');

        $this->_url['plugin'] = $this->_plugin;
    }

    /**
     * Clears the state used for requests.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload('Core');
        unset($this->_url);
    }

    /**
     * Prepare url.
     *
     * @param array $url
     * @return array
     */
    protected function _getUrl(array $url = [])
    {
        return Hash::merge($this->_url, $url);
    }
}
