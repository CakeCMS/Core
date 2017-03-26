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

namespace Core\Test\TestCase\ORM\Behavior;

use Cake\ORM\Entity;
use Core\ORM\Table;
use Cake\ORM\TableRegistry;
use Core\TestSuite\TestCase;
use Core\ORM\Behavior\ProcessBehavior;

/**
 * Class ProcessBehaviorTest
 *
 * @package Core\Test\TestCase\ORM\Behavior
 */
class ProcessBehaviorTest extends TestCase
{

    public $fixtures = ['plugin.core.process_behavior'];

    public function testClassName()
    {
        $table = $this->_table();
        self::assertInstanceOf('Core\Test\TestCase\ORM\Behavior\RowsTable', $table);
        self::assertTrue(in_array('Process', $table->behaviors()->loaded()));
    }

    public function testCustomModelProcessMethod()
    {
        $ids = [2 => 2, 8 => 8];
        $result = $this->_table()->process('drug', $ids);
        self::assertSame($ids, $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDisableAction()
    {
        $table = $this->_table();
        $table->process('advert');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAction()
    {
        $table = $this->_table();
        $table->process('action');
    }

    public function testProcessBehaviorMethod()
    {
        $ids = [2 => 2, 8 => 8];
        $result = $this->_table()->process('delete', $ids);
        self::assertSame(1, $result);
    }

    public function testProcessDelete()
    {
        $table  = $this->_table();
        /** @var Entity $entity */
        $entity = $table->get(1);

        self::assertSame(1, $entity->id);

        $result = $table->processDelete([1, 2]);
        self::assertSame(2, $result);
    }

    public function testProcessPublish()
    {
        $table = $this->_table();
        /** @var Entity $entity */
        $entity = $table->get(2);
        self::assertSame(0, $entity->get('status'));

        $entity = $table->get(4);
        self::assertSame(0, $entity->get('status'));

        $result = $table->processPublish([1, 2, 4]);
        self::assertSame(3, $result);

        $entity = $table->get(2);
        self::assertSame(1, $entity->get('status'));

        $entity = $table->get(4);
        self::assertSame(1, $entity->get('status'));
    }

    public function testProcessUnPublish()
    {
        $table  = $this->_table();
        /** @var Entity $entity */
        $entity = $table->get(1);
        self::assertSame(1, $entity->get('status'));

        $entity = $table->get(3);
        self::assertSame(1, $entity->get('status'));

        $result = $table->processUnPublish([1, 3]);
        self::assertSame(2, $result);

        $entity = $table->get(1);
        self::assertSame(0, $entity->get('status'));

        $entity = $table->get(3);
        self::assertSame(0, $entity->get('status'));
    }

    /**
     * @return RowsTable
     */
    protected function _table()
    {
        return TableRegistry::get('Rows', [
            'className' => __NAMESPACE__ . '\RowsTable'
        ]);
    }
}

/**
 * Class RowsTable
 *
 * @package Core\Test\TestCase\ORM\Behavior
 * @property ProcessBehavior $Process
 * @method process($name, array $ids = [])
 * @method processDelete(array $ids)
 * @method processPublish(array $ids)
 * @method processUnPublish(array $ids)
 */
class RowsTable extends Table
{
    /**
     * Default schema
     *
     * @var array
     */
    protected static $_tableSchema = [
        'id'           => ['type' => 'integer'],
        'title'        => ['type' => 'string'],
        'alias'        => ['type' => 'string'],
        'status'       => ['type' => 'integer'],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

    /**
     * Initializes the schema.
     *
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->setSchema(self::$_tableSchema);
        $this->setTable('process_behavior');
        $this->addBehavior('Core.Process', [
            'actions' => [
                'advert' => false,
                'drug'   => 'processDrug'
            ]
        ]);
    }

    /**
     * Process drug method.
     *
     * @param array $ids
     * @return array
     */
    public function processDrug(array $ids = [])
    {
        return $ids;
    }
}
