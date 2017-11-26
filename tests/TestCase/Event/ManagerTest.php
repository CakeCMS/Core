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

use Core\Plugin;
use Test\Cases\TestCase;
use Core\Event\EventManager;
use Test\Controller\EventController;

/**
 * Class ManagerTest
 *
 * @package Core\Test\TestCase\Event
 */
class ManagerTest extends TestCase
{

    public function testLoadListeners()
    {
        EventManager::loadListeners();
        $manager   = EventManager::instance();
        $listeners = $manager->listeners('Controller.setup');

        self::assertTrue(is_array($listeners));
        self::assertTrue(!empty($listeners));
    }

    public function testTrigger()
    {
        Plugin::load('Test', ['autoload' => true]);
        EventManager::loadListeners();

        $controller = new EventController();
        self::assertSame('Event.Controller.index', $controller->index());
        self::assertSame('Event.Controller.view', $controller->view());
        
        Plugin::unload('Test');
    }
}
