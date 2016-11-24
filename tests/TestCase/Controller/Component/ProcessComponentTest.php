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

namespace Core\Test\TestCase\Controller\Component;

use Core\ORM\Table;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Core\TestSuite\TestCase;
use Core\Controller\AppController;
use Cake\Routing\Route\DashedRoute;
use Core\ORM\Behavior\ProcessBehavior;
use Cake\Controller\ComponentRegistry;
use Core\Controller\Component\ProcessComponent;

/**
 * Class ProcessComponentTest
 *
 * @package Core\Test\TestCase\Controller\Component
 */
class ProcessComponentTest extends TestCase
{

    public $fixtures = ['plugin.core.process_behavior'];

    /**
     * @var AppController
     */
    protected $_controller;

    /**
     * @var ProcessComponent
     */
    protected $_process;

    public function setUp()
    {
        parent::setUp();

        Router::scope('/', function ($routes) {
            $routes->fallbacks(DashedRoute::class);
        });

        $this->_controller = new AppController();
        $componentRegistry = new ComponentRegistry($this->_controller);
        $this->_process    = new ProcessComponent($componentRegistry);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_controller, $this->_process);
    }

    public function testGetRequestVars()
    {
        $this->_process->request->data = [
            'action' => 'test',
            'user' => [
                1 => [
                    'id' => 1,
                ],
                4 => [
                    'id' => 1,
                ],
                5 => [
                    'id' => 0,
                ],
                8 => [
                    'id' => 1,
                ],
            ]
        ];

        list ($action, $ids) = $this->_process->getRequestVars('users');

        $this->assertSame('test', $action);
        $this->assertSame([
            1 => 1,
            4 => 4,
            8 => 8,
        ], $ids);

        $this->_process->request->data = [
            'action' => 'new-action',
            'user' => [
                4 => [
                    'id' => 1,
                ],
                5 => '1',
                9 => [
                    'id' => '9',
                ],
            ]
        ];

        list ($action, $ids) = $this->_process->getRequestVars('users');
        $this->assertSame('new-action', $action);
        $this->assertSame([4 => 4], $ids);
    }

    public function testMakeNotFoundAction()
    {
        $table  = new Table();
        $result = $this->_process->make($table, '', [
            'user' => [
                4 => [
                    'id' => 1,
                ],
            ]
        ]);

        $session = $this->_controller->request->session()->read('Flash.flash');

        $this->assertInstanceOf('Cake\Network\Response', $result);
        $this->assertTrue(is_array($result->header()));
        $this->assertSame(['Location' => 'http://localhost/'], $result->header());
        $this->assertSame(__d('core', 'Action not found.'), $session[0]['message']);
    }

    public function testMakeNoChooseItems()
    {
        $table   = new Table();
        $result  = $this->_process->make($table, 'delete', []);
        $session = $this->_controller->request->session()->read('Flash.flash');

        $this->assertInstanceOf('Cake\Network\Response', $result);
        $this->assertTrue(is_array($result->header()));
        $this->assertSame(['Location' => 'http://localhost/'], $result->header());
        $this->assertSame(__d('core', 'Please choose only one item.'), $session[0]['message']);
    }

    public function testMakeSuccess()
    {
        $table  = $this->_table();

        $this->_controller->request->addParams([
            'prefix' => 'admin'
        ]);

        $result = $this->_process->make($table, 'delete', [
            1 => 1,
            4 => 4,
        ]);

        $session = $this->_controller->request->session()->read('Flash.flash');

        $this->assertInstanceOf('Cake\Network\Response', $result);
        $this->assertTrue(is_array($result->header()));
        $this->assertSame(['Location' => 'http://localhost/'], $result->header());
        $this->assertSame('<strong>2</strong> records success removed', $session[0]['message']);
    }

    public function testMakeFail()
    {
        $table   = $this->_table();
        $result  = $this->_process->make($table, 'delete', [9 => 9]);
        $session = $this->_controller->request->session()->read('Flash.flash');

        $this->assertTrue(is_array($result->header()));
        $this->assertSame(['Location' => 'http://localhost/'], $result->header());
        $this->assertSame(__d('core', 'An error has occurred. Please try again.'), $session[0]['message']);
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
 * @package Core\Test\TestCase\Controller\Component
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
    protected $_schema = [
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
        $this->schema($this->_schema);
        $this->table('process_behavior');
    }
}
