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

namespace Test\Controller;

use Core\Event\EventManager;
use Core\Controller\AppController;


/**
 * Class EventController
 *
 * @package Test\Controller
 */
class EventController extends AppController
{

    /**
     * Index action.
     *
     * @return mixed
     */
    public function index()
    {
        $event = EventManager::trigger('Event.Controller.index', $this);
        return $event->result;
    }

    /**
     * View action.
     *
     * @return mixed
     */
    public function view()
    {
        $event = EventManager::trigger('Event.Controller.view');
        return $event->result;
    }

    /**
     * Process action.
     *
     * @return void
     */
    public function process()
    {
    }
}
