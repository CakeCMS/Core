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

namespace Core\ORM\Behavior;

use JBZoo\Utils\Arr;
use Cake\ORM\Behavior;

/**
 * Class ProcessBehavior
 *
 * @package Core\ORM\Behavior
 */
class ProcessBehavior extends Behavior
{
    /**
     * Default config.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'field' => 'status',
        'actions' => [
            'delete'    => 'processDelete',
            'publish'   => 'processPublish',
            'unpublish' => 'processUnPublish'
        ],
    ];

    /**
     * Process table method.
     *
     * @param string $name
     * @param array $ids
     * @return mixed
     */
    public function process($name, array $ids = [])
    {
        $allowActions = $this->getConfig('actions');

        if (!Arr::key($name, $allowActions)) {
            throw new \InvalidArgumentException(__d('core', 'Invalid action to perform'));
        }

        $action = $allowActions[$name];
        if ($action === false) {
            throw new \InvalidArgumentException(__d('core', 'Action "{0}" is disabled', $name));
        }

        if (Arr::in($action, get_class_methods($this->_table))) {
            return $this->_table->{$action}($ids);
        }

        return $this->{$action}($ids);
    }

    /**
     * Process delete method.
     *
     * @param array $ids
     * @return int
     */
    public function processDelete(array $ids)
    {
        return $this->_table->deleteAll([
            $this->_table->getPrimaryKey() . ' IN (' . implode(',', $ids) . ')'
        ]);
    }

    /**
     * Process publish method.
     *
     * @param array $ids
     * @return int
     */
    public function processPublish(array $ids)
    {
        return $this->_toggleField($ids, STATUS_PUBLISH);
    }

    /**
     * Process un publish method.
     *
     * @param array $ids
     * @return int
     */
    public function processUnPublish(array $ids)
    {
        return $this->_toggleField($ids, STATUS_UN_PUBLISH);
    }

    /**
     * Toggle table field.
     *
     * @param array $ids
     * @param int $value
     * @return int
     */
    protected function _toggleField(array $ids, $value = STATUS_UN_PUBLISH)
    {
        return $this->_table->updateAll([
            $this->_configRead('field') => $value,
        ], [
            $this->_table->getPrimaryKey() . ' IN (' . implode(',', $ids) . ')'
        ]);
    }
}
