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

use Cake\ORM\Entity;
use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\Utility\Hash;

/**
 * Class CachedBehavior
 *
 * @package Core\ORM\Behavior
 */
class CachedBehavior extends Behavior
{

    /**
     * Default config.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'config' => 'default',
        'prefix' => null,
        'groups' => [],
    ];

    /**
     * Initialize hook.
     *
     * @param array $config The config for this behavior.
     * @return void
     */
    public function initialize(array $config)
    {
        $config = Hash::merge($this->_defaultConfig, $config);
        $this->setConfig($config);
    }

    /**
     * Clear cache group after save.
     *
     * @param Event $event
     * @param Entity $entity
     * @param \ArrayObject $options
     * @SuppressWarnings("unused")
     */
    public function afterSave(Event $event, Entity $entity, \ArrayObject $options)
    {
        $this->_clearCacheGroup();
    }

    /**
     * Clear cache group after delete.
     *
     * @param Event $event
     * @param Entity $entity
     * @param \ArrayObject $options
     * @SuppressWarnings("unused")
     */
    public function afterDelete(Event $event, Entity $entity, \ArrayObject $options)
    {
        $this->_clearCacheGroup();
    }

    /**
     * Clear cache group.
     *
     * @return void
     */
    protected function _clearCacheGroup()
    {
        $cacheGroups = (array) $this->getConfig('groups');
        foreach ($cacheGroups as $group) {
            Cache::clearGroup($group, $this->getConfig('config'));
        }
    }
}
