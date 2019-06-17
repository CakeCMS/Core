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

namespace Core\Controller;

use Core\Core\Plugin;
use Cake\Event\Event;
use Cake\Http\Response;
use Core\Event\EventManager;
use Core\Controller\Component\AppComponent;
use Core\Controller\Component\MoveComponent;
use Core\Controller\Component\FlashComponent;
use Core\Controller\Component\ProcessComponent;
use Cake\Controller\Controller as CakeController;
use Cake\Controller\Component\RequestHandlerComponent;

/**
 * Class AppController
 *
 * @package     Core\Controller
 * @property    AppComponent $App
 * @property    MoveComponent $Move
 * @property    FlashComponent $Flash
 * @property    ProcessComponent $Process
 * @property    \Cake\Http\Response $response
 * @property    RequestHandlerComponent $RequestHandler
 */
class AppController extends CakeController
{

    /**
     * Initialization hook method.
     *
     * @return  void
     */
    public function initialize()
    {
        parent::initialize();
        $this->_setTheme();

        $pluginEvent = Plugin::getData('Core', 'Controller.initialize');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('Controller.initialize')) {
            call_user_func_array($pluginEvent->find(0), [$this]);
        }
    }

    /**
     * Called after the controller action is run, but before the view is rendered. You can use this method
     * to perform logic or set view variables that are required on every request.
     *
     * @param   \Cake\Event\Event $event The beforeRender event.
     *
     * @return  void
     */
    public function beforeRender(Event $event)
    {
        $pluginEvent = Plugin::getData('Core', 'Controller.beforeRender');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('Controller.beforeRender')) {
            call_user_func_array($pluginEvent->find(0), [$this, $event]);
        }

        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->getType(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param   Event $event
     *
     * @return  void
     */
    public function beforeFilter(Event $event)
    {
        EventManager::trigger('Controller.setup', $this);

        $pluginEvent = Plugin::getData('Core', 'Controller.beforeFilter');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('Controller.beforeFilter')) {
            call_user_func_array($pluginEvent->find(0), [$this, $event]);
        }
    }

    /**
     * The beforeRedirect method is invoked when the controller's redirect method is called but before any
     * further action.
     *
     * @param   Event $event
     * @param   array|string $url
     * @param   Response $response
     *
     * @return  void
     */
    public function beforeRedirect(Event $event, $url, Response $response)
    {
        $pluginEvent = Plugin::getData('Core', 'Controller.beforeRedirect');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('Controller.beforeRedirect')) {
            call_user_func_array($pluginEvent->find(0), [$this, $event, $url, $response]);
        }
    }

    /**
     * Called after the controller action is run and rendered.
     *
     * @param   Event $event
     *
     * @return  void
     */
    public function afterFilter(Event $event)
    {
        $pluginEvent = Plugin::getData('Core', 'Controller.afterFilter');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('Controller.afterFilter')) {
            call_user_func_array($pluginEvent->find(0), [$this, $event]);
        }
    }

    /**
     * Setup application theme.
     *
     * @return  void
     */
    protected function _setTheme()
    {
        $theme = $this->request->getParam('theme');
        if ($theme) {
            $this->viewBuilder()->setTheme($theme);
        }
    }
}
