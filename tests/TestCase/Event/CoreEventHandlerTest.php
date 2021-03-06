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

namespace Core\Test\TestCase\Event;

use Core\Nav;
use Core\Event\EventManager;
use Test\Cases\IntegrationTestCase;

/**
 * Class CoreEventHandlerTest
 *
 * @package Core\Test\TestCase\Event
 */
class CoreEventHandlerTest extends IntegrationTestCase
{

    public function testOnSetupAdminData()
    {
        $this->loadPlugins(['Core']);

        EventManager::loadListeners();

        self::assertTrue((count(Nav::items('sidebar')) > 0));

        $this->get($this->_getUrl([
            'prefix'     => 'admin',
            'controller' => 'Root',
            'action'     => 'dashboard'
        ]));

        $nav = Nav::items('sidebar');

        self::assertTrue(!empty($nav));
        self::assertArrayHasKey('dashboard', $nav);
        self::assertInstanceOf('Core\Controller\Admin\RootController', $this->_controller);
    }
}
