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

use Cake\ORM\Table;
use JBZoo\Data\Data;
use JBZoo\Data\JSON;
use JBZoo\Utils\Arr;
use JBZoo\Utils\Str;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Core\Event\EventManager;

/**
 * Class ProcessComponent
 *
 * @package Core\Controller\Component
 * @property FlashComponent $Flash
 */
class ProcessComponent extends AppComponent
{

    const PRIMARY_KEY = 'id';
    const EVENT_NAME_BEFORE = 'Before';
    const EVENT_NAME_AFTER = 'After';

    /**
     * Other Components this component uses.
     *
     * @var array
     */
    public $components = [
        'Core.Flash'
    ];

    /**
     * Get actual request vars for process.
     *
     * @param string $name
     * @param string $primaryKey
     * @return array
     */
    public function getRequestVars($name, $primaryKey = self::PRIMARY_KEY)
    {
        $name = Str::low(Inflector::singularize($name));
        $requestIds = (array) $this->request->data($name);
        $action = $this->request->data('action');
        $ids = $this->_getIds($requestIds, $primaryKey);

        return [$action, $ids];
    }

    /**
     * Constructor hook method.
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config)
    {
        $_config = [
            'context'  => __d('core', 'record'),
            'redirect' => [
                'action'     => 'index',
                'prefix'     => $this->request->param('prefix'),
                'plugin'     => $this->request->param('plugin'),
                'controller' => $this->request->param('controller'),
            ],
            'messages' => [
                'no_action' => __d('core', 'Action not found.'),
                'no_choose' => __d('core', 'Please choose only one item.'),
            ]
        ];

        $config = Hash::merge($_config, $config);
        $this->config($config);

        parent::initialize($config);
    }

    /**
     * Make process.
     *
     * @param Table $table
     * @param string $action
     * @param array $ids
     * @param array $options
     * @return \Cake\Network\Response|null
     */
    public function make(Table $table, $action, array $ids = [], array $options = [])
    {
        $count       = count($ids);
        $options     = $this->_getOptions($options, $count);
        $redirectUrl = $options['redirect'];
        $messages    = new JSON($options['messages']);

        $event = EventManager::trigger($this->_getEventName($action), $this->_controller, ['ids' => $ids]);
        $ids   = $event->data->get('ids', $ids);
        $count = count($ids);

        if (!$action) {
            $this->Flash->error($messages->get('no_action'));
            return $this->_controller->redirect($redirectUrl);
        }

        if ($count <= 0) {
            $this->Flash->error($messages->get('no_choose'));
            return $this->_controller->redirect($redirectUrl);
        }

        $this->_loadBehavior($table);
        if ($table->process($action, $ids)) {
            return $this->_process($action, $messages, $redirectUrl, $ids);
        }

        $this->Flash->error(__d('core', 'An error has occurred. Please try again.'));
        return $this->_controller->redirect($redirectUrl);
    }

    /**
     * Setup default action messages.
     *
     * @param int $count
     * @return array
     */
    protected function _getDefaultMessages($count)
    {
        $context       = $this->_configRead('context');
        $contextPlural = Inflector::pluralize($context);
        $countString   = sprintf('<strong>%s</strong>', $count);
        return [
            'delete' => __dn(
                'core',
                'One ' . $context . ' success removed',
                '{0} ' . $contextPlural . ' success removed',
                $count,
                $countString
            ),
            'publish' => __dn(
                'core',
                'One ' . $context . ' success publish',
                '{0} ' . $contextPlural . ' success published',
                $count,
                $countString
            ),
            'unpublish' => __dn(
                'core',
                'One ' . $context . ' success unpublish',
                '{0} ' . $contextPlural . ' success unpublished',
                $count,
                $countString
            ),
        ];
    }

    /**
     * Get event name by data.
     *
     * @param string $action
     * @param string $event
     * @return string
     */
    protected function _getEventName($action, $event = self::EVENT_NAME_BEFORE)
    {
        $details = [];
        if ($prefix = $this->request->param('prefix')) {
            $details[] = ucfirst($prefix);
        }

        $details = Hash::merge($details, [
            'Controller',
            $this->_controller->name,
            $event . Inflector::camelize($action),
            'Process',
        ]);

        return implode('.', $details);
    }

    /**
     * Get ids by request.
     *
     * @param array $ids
     * @param string $primaryKey
     * @return array
     */
    protected function _getIds(array $ids, $primaryKey = self::PRIMARY_KEY)
    {
        $return = [];
        foreach ($ids as $id => $value) {
            if (is_array($value) && is_int($id) && (int) $value[$primaryKey] === 1) {
                $return[$id] = $id;
            }
        }

        return $return;
    }

    /**
     * Create and merge actual process options.
     *
     * @param array $options
     * @param int|string $count
     * @return array
     */
    protected function _getOptions(array $options, $count)
    {
        $options = Hash::merge($this->_config, $options);
        return Hash::merge(['messages' => $this->_getDefaultMessages($count)], $options);
    }

    /**
     * Load process behavior.
     *
     * @param Table $table
     */
    protected function _loadBehavior(Table $table)
    {
        $behaviors = $table->behaviors();
        if (!Arr::in('Process', $behaviors->loaded())) {
            $behaviors->load('Core.Process');
        }
    }

    /**
     * Controller process.
     *
     * @param string $action
     * @param Data $messages
     * @param array $redirect
     * @param array $ids
     * @return \Cake\Network\Response|null
     */
    protected function _process($action, Data $messages, array $redirect, array $ids)
    {
        $count = count($ids);
        $defaultMsg = __dn(
            'core',
            'One record success processed',
            '{0} records processed',
            $count,
            sprintf('<strong>%s</strong>', $count)
        );

        EventManager::trigger($this->_getEventName($action, self::EVENT_NAME_AFTER), $this->_controller, [
            'ids' => $ids
        ]);

        $this->Flash->success($messages->get($action, $defaultMsg));
        return $this->_controller->redirect($redirect);
    }
}
