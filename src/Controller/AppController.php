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

use Core\Cms;
use Core\Plugin;
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
 * @package Core\Controller
 * @property \Cake\Http\Response $response
 * @property AppComponent $App
 * @property MoveComponent $Move
 * @property RequestHandlerComponent $RequestHandler
 * @property FlashComponent $Flash
 * @property ProcessComponent $Process
 */
class AppController extends CakeController
{

    /**
     * Hold CMS object.
     *
     * @var Cms
     */
    public $cms;

    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->cms = Cms::getInstance();
        $this->_setTheme();
        Plugin::manifestEvent('Controller.initialize', $this);
    }

    /**
     * Called after the controller action is run, but before the view is rendered. You can use this method
     * to perform logic or set view variables that are required on every request.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        Plugin::manifestEvent('Controller.beforeRender', $this, $event);

        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param Event $event
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        EventManager::trigger('Controller.setup', $this);
        Plugin::manifestEvent('Controller.beforeFilter', $this, $event);
    }

    /**
     * The beforeRedirect method is invoked when the controller's redirect method is called but before any
     * further action.
     *
     * @param Event $event
     * @param array|string $url
     * @param Response $response
     * @return void
     */
    public function beforeRedirect(Event $event, $url, Response $response)
    {
        Plugin::manifestEvent('Controller.beforeRedirect', $this, $event, $url, $response);
    }

    /**
     * Called after the controller action is run and rendered.
     *
     * @param Event $event
     * @return void
     */
    public function afterFilter(Event $event)
    {
        Plugin::manifestEvent('Controller.afterFilter', $this, $event);
    }

    /**
     * Setup application theme.
     *
     * @return void
     */
    protected function _setTheme()
    {
        if ($this->request->is('theme')) {
            $theme = $this->request->getParam('theme');
            $this->viewBuilder()->setTheme($theme);
        }
    }
}
