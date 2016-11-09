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
use JBZoo\Utils\Arr;
use Cake\Controller\Component;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior\TreeBehavior;

/**
 * Class MoveComponent
 *
 * @package Core\Controller\Component
 * @property FlashComponent $Flash
 */
class MoveComponent extends AppComponent
{

    const TYPE_UP   = 'moveUp';
    const TYPE_DOWN = 'moveDown';

    /**
     * Other Components this component uses.
     *
     * @var array
     */
    public $components = [
        'Core.Flash'
    ];

    /**
     * Reading the whole config.
     *
     * @param null|string $key
     * @param null|string $value
     * @param bool $merge
     * @return mixed
     */
    public function config($key = null, $value = null, $merge = true)
    {
        $this->_defaultConfig = [
            'messages' => [
                'success' => __d('core', 'Object has been moved'),
                'error'   => __d('core', 'Object could not been moved')
            ],
            'action' => 'index',
        ];

        return parent::config($key, $value, $merge);
    }

    /**
     * Move down record in tree.
     *
     * @param Table $table
     * @param EntityInterface $entity
     * @param int $step
     * @return \Cake\Network\Response|null
     */
    public function down(Table $table, EntityInterface $entity, $step = 1)
    {
        return $this->_move($table, $entity, $step, self::TYPE_DOWN);
    }

    /**
     * Move up record in tree.
     *
     * @param Table $table
     * @param EntityInterface $entity
     * @param int $step
     * @return \Cake\Network\Response|null
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function up(Table $table, EntityInterface $entity, $step = 1)
    {
        return $this->_move($table, $entity, $step);
    }

    /**
     * Move object in tree table.
     *
     * @param Table $table
     * @param EntityInterface $entity
     * @param string $type
     * @param int $step
     * @return \Cake\Network\Response|null
     */
    protected function _move(Table $table, EntityInterface $entity, $step = 1, $type = self::TYPE_UP)
    {
        $behaviors = $table->behaviors();
        if (!Arr::in('Tree', $behaviors->loaded())) {
            $behaviors->load('Tree');
        }

        /** @var TreeBehavior $treeBehavior */
        $treeBehavior = $behaviors->get('Tree');
        $treeBehavior->config('scope', $entity->get('id'));

        if ($table->{$type}($entity, $step)) {
            $this->Flash->success($this->_configRead('messages.success'));
        } else {
            $this->Flash->error($this->_configRead('messages.error'));
        }

        return $this->_redirect();
    }

    /**
     * Process redirect.
     *
     * @return \Cake\Network\Response|null
     */
    protected function _redirect()
    {
        return $this->_controller->redirect([
            'prefix'     => $this->request->param('prefix'),
            'plugin'     => $this->request->param('plugin'),
            'controller' => $this->request->param('controller'),
            'action'     => $this->_configRead('action'),
        ]);
    }
}
