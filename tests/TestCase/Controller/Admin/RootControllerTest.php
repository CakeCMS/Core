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

namespace Core\Test\TestCase\Controller\Admin;

use Cake\Routing\DispatcherFactory;
use Core\Plugin;
use Test\Cases\IntegrationTestCase;

/**
 * Class RootControllerTest
 *
 * @package Core\Test\TestCase
 */
class RootControllerTest extends IntegrationTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->loadPlugins(['Core']);
    }

    public function testDashboard()
    {
        $url = $this->_getUrl([
            'prefix'     => 'admin',
            'controller' => 'Root',
            'action'     => 'dashboard',
        ]);

        $this->get($url);
        $this->assertResponseOk();
    }
}
