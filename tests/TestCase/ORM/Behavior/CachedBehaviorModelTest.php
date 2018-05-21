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

namespace Core\Test\TestCase\ORM\Behavior;

use Cake\Cache\Cache;
use Test\Cases\TestCase;
use Cake\ORM\TableRegistry;

/**
 * Class CachedBehaviorModelTest
 *
 * @package Core\Test\TestCase\ORM\Behavior
 */
class CachedBehaviorModelTest extends TestCase
{

    public $fixtures = ['plugin.core.cached_behavior',];

    protected $_plugin = 'Test';
    
    public function tearDown()
    {
        parent::tearDown();
        Cache::drop('test_cached');
    }

    public function testCached()
    {
        $table = TableRegistry::getTableLocator()->get('Test.CachedBehavior');
        $entity = $table->find()
            ->where(['id' => 2])
            ->cache('test_cached', 'test_cached')->first();

        $cacheFile = CACHE . 'query/cache/cached/cache_test_cached';
        self::assertTrue(file_exists($cacheFile));

        $entity->set('title', 'Cache');
        $table->save($entity);
        self::assertFalse(file_exists($cacheFile));

        $entity = $table->find()
            ->where(['id' => 1])
            ->cache('test_cached', 'test_cached')->first();

        $table->delete($entity);
        self::assertFalse(file_exists($cacheFile));
    }
}
