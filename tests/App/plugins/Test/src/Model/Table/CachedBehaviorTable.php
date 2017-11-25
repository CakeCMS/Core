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

namespace Test\Model\Table;

use Core\ORM\Table;

/**
 * Class CachedBehaviorTable
 *
 * @package Test\Model\Table
 */
class CachedBehaviorTable extends Table
{

    /**
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('cached_behavior');
        $this->primaryKey('id');
        $this->displayField('title');
        $this->behaviors()->load('Core.Cached', [
            'config' => 'test_cached',
            'groups' => ['cached'],
        ]);
    }
}
