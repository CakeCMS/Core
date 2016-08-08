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

use Core\Plugin;
use Core\Event\EventManager;
use Core\TestSuite\TestCase;
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

        $this->assertTrue(is_array($listeners));
        $this->assertTrue(!empty($listeners));
    }

    public function testTrigger()
    {
        Plugin::load('Test', ['autoload' => true]);
        EventManager::loadListeners();

        $controller = new EventController();
        $this->assertSame('Event.Controller.index', $controller->index());
        $this->assertSame('Event.Controller.view', $controller->view());
        
        Plugin::unload('Test');
    }
}
