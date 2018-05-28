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
     * @return  array
     * @return  void
     */
    public function implementedEvents()
    {
        return [
            'Controller.setup' => 'onControllerSetup'
        ];
    }

    /**
     * On controller setup.
     *
     * @param   Event $event
     * @return  void
     */
    public function onControllerSetup(Event $event)
    {
        /** @var AppController $controller */
        $controller = $event->getSubject();
        $isAdmin    = ($controller->request->getParam('prefix') === 'admin');

        $plugins = Plugin::loaded();
        foreach ($plugins as $plugin) {
            $path     = Plugin::path($plugin);
            $menuFile = ($isAdmin) ? 'admin_menu' : 'menu';
            $navConf  = $path . 'config/' . $menuFile . '.php';

            if (FS::isFile($navConf)) {
                /** @noinspection PhpIncludeInspection */
                require_once $navConf;
            }
        }
    }

}
