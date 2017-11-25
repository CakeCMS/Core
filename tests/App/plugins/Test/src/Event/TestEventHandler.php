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

namespace Test\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

/**
 * Class TestEventHandler
 *
 * @package Test\Event
 */
class TestEventHandler implements EventListenerInterface
{

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Event.Controller.index' => 'onIndex',
            'Event.Controller.view'  => 'onView',
        ];
    }

    /**
     * @param Event $event
     * @return string
     */
    public function onIndex(Event $event)
    {
        return $event->name();
    }

    /**
     * @param Event $event
     * @return string
     */
    public function onView(Event $event)
    {
        return $event->name();
    }
}
