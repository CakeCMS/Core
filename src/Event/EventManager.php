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

namespace Core\Event;

use Core\Plugin;
use Cake\Core\App;
use Cake\Event\EventManager as CakeEventManager;

/**
 * Class Manager
 *
 * @package Core\Event
 */
class EventManager extends CakeEventManager
{

    /**
     * Load Event Handlers during bootstrap.
     *
     * Plugins can add their own custom EventHandler in Config/events.php
     * with the following format:
     *
     * 'events' => [
     *      'Core.CoreEventHandler' => [
     *          'options' = [
     *              'callable' => '',
     *              'priority' => '',
     *          ]
     *      ]
     * ]
     *
     * @return void
     */
    public static function loadListeners()
    {
        $manager = self::instance();
        $plugins = Plugin::loaded();

        foreach ($plugins as $plugin) {
            $events = Plugin::getData($plugin, 'events')->getArrayCopy();
            foreach ($events as $name => $config) {
                if (is_numeric($name)) {
                    $name   = $config;
                    $config = [];
                }

                $class  = App::className($name, 'Event');
                $config = (array) $config;

                if ($class !== false) {
                    $listener = new $class($config);
                    $manager->on($listener, $config);
                }
            }
        }
    }

    /**
     * Emits an event.
     *
     * @param string $name
     * @param object|null $subject
     * @param array|null $data
     * @return Event
     */
    public static function trigger($name, $subject = null, $data = null)
    {
        $event = new Event($name, $subject, $data);
        if (is_object($subject)) {
            return $subject->eventManager()->dispatch($event);
        }

        return EventManager::instance()->dispatch($event);
    }
}
