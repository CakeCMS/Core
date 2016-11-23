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

namespace Core\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Utility\Hash;
use JBZoo\Utils\Str;

/**
 * Class AppComponent
 *
 * @package Core\Controller\Component
 */
class AppComponent extends Component
{

    /**
     * Controller object.
     *
     * @var Controller
     */
    protected $_controller;

    /**
     * Constructor hook method.
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_controller = $this->_registry->getController();
    }

    /**
     * Redirect by request data.
     *
     * @param array $options
     * @return \Cake\Network\Response|null
     */
    public function redirect(array $options = [])
    {
        $plugin     = $this->request->param('plugin');
        $controller = $this->request->param('controller');

        $_options = [
            'apply'   => [],
            'savenew' => [
                'plugin'     => $plugin,
                'controller' => $controller,
                'action'     => 'add'
            ],
            'save' => [
                'plugin'     => $plugin,
                'controller' => $controller,
                'action'     => 'index',
            ]
        ];

        $options = Hash::merge($_options, $options);

        $url = $options['save'];
        if ($rAction = $this->request->data('action')) {
            list(, $action) = pluginSplit($rAction);
            $action = Str::low($action);
            if (isset($options[$action])) {
                $url = $options[$action];
            }
        }

        return $this->_controller->redirect($url);
    }
}
