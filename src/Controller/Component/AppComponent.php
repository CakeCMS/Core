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

namespace Core\Controller\Component;

use Core\ORM\Table;
use JBZoo\Utils\Arr;
use JBZoo\Utils\Str;
use Cake\Utility\Hash;
use Cake\Http\ServerRequest;
use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Network\Exception\BadRequestException;

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
     * Hold controller request (Server request).
     *
     * @var ServerRequest
     */
    protected $_request;

    /**
     * Constructor hook method.
     *
     * @param   array $config
     * @return  void
     */
    public function initialize(array $config)
    {
        $this->_controller = $this->_registry->getController();
        $this->_request    = $this->_controller->request;

        parent::initialize($config);
    }

    /**
     * Redirect by request data.
     *
     * @param   array $options
     * @return  \Cake\Http\Response|null
     */
    public function redirect(array $options = [])
    {
        $plugin     = $this->_request->getParam('plugin');
        $controller = $this->_request->getParam('controller');

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
        if ($rAction = $this->_request->getData('action')) {
            list(, $action) = pluginSplit($rAction);
            $action = Str::low($action);
            if (Arr::key($action, $options)) {
                $url = $options[$action];
            }
        }

        return $this->_controller->redirect($url);
    }

    /**
     * Toggle table field value.
     *
     * @param   Table $table
     * @param   int $id
     * @param   string|int $value
     * @param   string $field
     */
    public function toggleField(Table $table, $id, $value, $field = 'status')
    {
        $this->checkIsAjax();
        $this->_checkToggleData($id, $value);

        $this->_controller->viewBuilder()
            ->setLayout('ajax')
            ->setTemplate('toggle')
            ->setTemplatePath('Common');

        $entity = $table->get($id);
        $entity->set($field, !(int) $value);

        if ($result = $table->save($entity)) {
            $this->_controller->set('entity', $result);
            $this->_controller->render('toggle');
        } else {
            throw new \RuntimeException(__d('core', 'Failed toggling field {0} to {1}', $field, $entity->get($field)));
        }
    }

    /**
     * Check is ajax request.
     *
     * @return  void
     */
    public function checkIsAjax()
    {
        if (!$this->_request->is('ajax')) {
            throw new \RuntimeException(__d('core', 'Bad request'));
        }
    }

    /**
     * Check toggle data.
     *
     * @param   int $id
     * @param   string|int $value
     */
    protected function _checkToggleData($id, $value)
    {
        if (empty($id) || $value === null) {
            throw new BadRequestException(__d('core', 'Invalid content'));
        }
    }
}
