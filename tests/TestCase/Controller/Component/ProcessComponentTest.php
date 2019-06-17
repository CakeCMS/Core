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

namespace Core\Test\TestCase\Controller\Component;

use Cake\Http\ServerRequest;
use Cake\ORM\Locator\TableLocator;
use Core\ORM\Table;
use Cake\Routing\Router;
use Test\Cases\TestCase;
use Cake\ORM\TableRegistry;
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

    public $fixtures = ['plugin.Core.ProcessBehavior'];

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
            /** @var \Cake\Routing\RouteBuilder $routes */
            $routes->fallbacks(DashedRoute::class);
        });

        $this->_controller = new AppController();
        $componentRegistry = new ComponentRegistry($this->_controller);
        $this->_process    = new ProcessComponent($componentRegistry, []);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_controller, $this->_process);
    }

    public function testGetRequestVars()
    {
        $request = new ServerRequest([
            'post' => [
                'action' => 'test',
                'user'   => [
                    1 => [
                        'id' => 1
                    ],
                    4 => [
                        'id' => 1
                    ],
                    5 => [
                        'id' => 0
                    ],
                    8 => [
                        'id' => 1
                    ]
                ]
            ]
        ]);

        $controller         = new AppController($request);
        $componentRegistry  = new ComponentRegistry($controller);
        $component          = new ProcessComponent($componentRegistry, []);

        $result = $component->getRequestVars('users');

        self::assertSame('test', $result[0]);
        self::assertSame([
            1 => 1,
            4 => 4,
            8 => 8,
        ], $result[1]);

        $request = new ServerRequest([
            'post' => [
                'action' => 'new-action',
                'user' => [
                    4 => [
                        'id' => 1
                    ],
                    5 => '1',
                    9 => [
                        'id' => '9'
                    ]
                ]
            ]
        ]);

        $controller         = new AppController($request);
        $componentRegistry  = new ComponentRegistry($controller);
        $component          = new ProcessComponent($componentRegistry, []);

        $result = $component->getRequestVars('users');
        self::assertSame('new-action', $result[0]);
        self::assertSame([4 => 4], $result[1]);
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

        $session = $this->_controller->request->getSession()->read('Flash.flash');

        self::assertInstanceOf('Cake\Http\Response', $result);
        self::assertSame(['http://localhost/'], $result->getHeader('Location'));
        self::assertSame(__d('core', 'Action not found.'), $session[0]['message']);
    }

    public function testMakeNoChooseItems()
    {
        $table   = new Table();
        $result  = $this->_process->make($table, 'delete', []);
        $session = $this->_controller->request->getSession()->read('Flash.flash');

        self::assertInstanceOf('Cake\Http\Response', $result);
        self::assertSame(['http://localhost/'], $result->getHeader('Location'));
        self::assertSame(__d('core', 'Please choose only one item.'), $session[0]['message']);
    }

    public function testMakeSuccess()
    {
        $table  = $this->_table();

        $this->_controller->request = $this->_controller->request->withParam('prefix', 'admin');

        $result = $this->_process->make($table, 'delete', [
            1 => 1,
            4 => 4
        ]);

        $session = $this->_controller->request->getSession()->read('Flash.flash');

        self::assertInstanceOf('Cake\Http\Response', $result);
        self::assertSame(['http://localhost/'], $result->getHeader('Location'));
        self::assertSame('<strong>2</strong> records success removed', $session[0]['message']);
    }

    public function testMakeFail()
    {
        $table   = $this->_table();
        $result  = $this->_process->make($table, 'delete', [9 => 9]);
        $session = $this->_controller->request->getSession()->read('Flash.flash');

        self::assertSame(['http://localhost/'], $result->getHeader('Location'));
        self::assertSame(__d('core', 'An error has occurred. Please try again.'), $session[0]['message']);
    }

    /**
     * @return \Cake\ORM\Table
     */
    protected function _table()
    {
        return TableRegistry::getTableLocator()->get('Rows', [
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
     * Default table schema
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
    }
}
