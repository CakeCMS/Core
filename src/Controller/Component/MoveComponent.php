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

use Cake\ORM\Table;
use JBZoo\Utils\Arr;
use Cake\Controller\Component;
use Cake\ORM\Behavior\TreeBehavior;

/**
 * Class MoveComponent
 *
 * @package     Core\Controller\Component
 * @property    FlashComponent $Flash
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
     * Move down record in tree.
     *
     * @param   Table $table
     * @param   int $id
     * @param   int $step
     * @return  \Cake\Http\Response|null
     */
    public function down(Table $table, $id, $step = 1)
    {
        return $this->_move($table, $id, $step, self::TYPE_DOWN);
    }

    /**
     * Sets the config.
     *
     * @param   array|string $key
     * @param   null|mixed $value
     * @param   bool $merge
     * @return  mixed
     * @throws  \Cake\Core\Exception\Exception When trying to set a key that is invalid.
     */
    public function setConfig($key, $value = null, $merge = true)
    {
        $this->_defaultConfig = [
            'messages' => [
                'success' => __d('core', 'Object has been moved'),
                'error'   => __d('core', 'Object could not been moved')
            ],
            'action' => 'index',
        ];

        return parent::setConfig($key, $value, $merge);
    }

    /**
     * Move up record in tree.
     *
     * @param   Table $table
     * @param   int $id
     * @param   int $step
     * @return  \Cake\Http\Response|null
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function up(Table $table, $id, $step = 1)
    {
        return $this->_move($table, $id, $step);
    }

    /**
     * Move object in tree table.
     *
     * @param   Table $table
     * @param   string $type
     * @param   int $id
     * @param   int $step
     * @return  \Cake\Http\Response|null
     */
    protected function _move(Table $table, $id, $step = 1, $type = self::TYPE_UP)
    {
        $behaviors = $table->behaviors();
        if (!Arr::in('Tree', $behaviors->loaded())) {
            $behaviors->load('Tree');
        }

        $entity = $table->get($id);

        /** @var TreeBehavior $treeBehavior */
        $treeBehavior = $behaviors->get('Tree');
        $treeBehavior->setConfig('scope', $entity->get('id'));

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
     * @return  \Cake\Http\Response|null
     */
    protected function _redirect()
    {
        $request = $this->_controller->request;
        return $this->_controller->redirect([
            'prefix'     => $request->getParam('prefix'),
            'plugin'     => $request->getParam('plugin'),
            'controller' => $request->getParam('controller'),
            'action'     => $this->getConfig('action'),
        ]);
    }
}
