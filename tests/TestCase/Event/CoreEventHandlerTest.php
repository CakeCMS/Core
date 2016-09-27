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

namespace Core\Test\TestCase\Event;

use Core\Nav;
use Core\Event\EventManager;
use Core\TestSuite\IntegrationTestCase;

/**
 * Class CoreEventHandlerTest
 *
 * @package Core\Test\TestCase\Event
 */
class CoreEventHandlerTest extends IntegrationTestCase
{

    public function testOnSetupAdminData()
    {
        EventManager::loadListeners();

        $this->assertSame([], Nav::items('sidebar'));

        $url = $this->_getUrl([
            'prefix'     => 'admin',
            'controller' => 'Root',
            'action'     => 'dashboard',
        ]);

        $this->get($url);

        $nav = Nav::items('sidebar');
        $this->assertTrue(!empty($nav));
        $this->assertArrayHasKey('dashboard', $nav);
        $this->assertInstanceOf('Core\Controller\Admin\RootController', $this->_controller);
    }
}