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
use JBZoo\Utils\FS;
use Core\Controller\AppController;
use Cake\Event\EventListenerInterface;

/**
 * Class CoreEventHandler
 *
 * @package Core\Event
 */
class CoreEventHandler implements EventListenerInterface
{

    /**
     * Returns a list of events this object is implementing.
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Controller.setup' => 'onControllerSetup'
        ];
    }

    /**
     * @param Event $event
     */
    public function onControllerSetup(Event $event)
    {
        /** @var AppController $controller */
        $controller = $event->subject();
        if ($controller->request->param('prefix') == 'admin') {
            $this->_onSetupAdmin($controller);
        }
    }

    /**
     * Setup admin data.
     *
     * @param AppController $controller
     * @SuppressWarnings("unused")
     */
    protected function _onSetupAdmin(AppController $controller)
    {
        $plugins = Plugin::loaded();
        foreach ($plugins as $plugin) {
            $path    = Plugin::path($plugin);
            $navConf = $path . 'config/admin_menu.php';

            if (FS::isFile($navConf)) {
                /** @noinspection PhpIncludeInspection */
                require_once $navConf;
            }
        }
    }
}
