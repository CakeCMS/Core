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
use Cake\Network\Request;
use Cake\Network\Session;
use Core\TestSuite\TestCase;
use Cake\Routing\Route\DashedRoute;
use Core\Controller\AppController;
use Core\Controller\Component\MoveComponent;

/**
 * Class MoveComponentTest
 *
 * @package Core\Test\TestCase\Controller\Component
 */
class MoveComponentTest extends TestCase
{

    public $fixtures = ['plugin.core.moves'];

    /**
     * @var Request
     */
    protected $_request;

    public function setUp()
    {
        parent::setUp();

        Router::scope('/', function ($routes) {
            $routes->fallbacks(DashedRoute::class);
        });

        $this->_request = new Request([
            'params' => [
                'prefix'     => false,
                'plugin'     => false,
                'controller' => 'Moves',
                'action'     => 'up',
                'pass'       => [],
            ],
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_component);
    }

    public function testUpFail()
    {
        $request = $this->_request;
        $controller = new MovesController($request);

        $entityId = 3;
        $result = $controller->up($entityId, 'text');

        $flashSession = $controller->request->session()->read('Flash.flash');
        $expected = [[
            'message' => __d('core', 'Object could not been moved'),
            'key'     => 'flash',
            'element' => 'Flash/error',
            'params'  => [],
        ]];

        $this->assertSame($expected, $flashSession);
        $this->assertSame(['Location' => 'http://localhost/moves'], $result->header());
    }

    public function testUpSuccess()
    {
        $request    = $this->_request;
        $controller = new MovesController($request);

        $entityId = 3;
        $entity   = $controller->Moves->get($entityId);

        $this->assertSame('Admin', $entity->get('name'));
        $this->assertSame($entityId, $entity->get('id'));
        $this->assertSame(1, $entity->get('parent_id'));
        $this->assertSame(4, $entity->get('lft'));
        $this->assertSame(5, $entity->get('rght'));

        $this->assertSame([], $controller->response->header());

        /** @var \Cake\Network\Response $result */
        $result = $controller->up($entityId);
        $entity = $controller->Moves->get($entityId);

        $this->assertSame(['Location' => 'http://localhost/moves'], $result->header());

        $this->assertSame('Admin', $entity->get('name'));
        $this->assertSame($entityId, $entity->get('id'));
        $this->assertSame(1, $entity->get('parent_id'));
        $this->assertSame(2, $entity->get('lft'));
        $this->assertSame(3, $entity->get('rght'));

        $flashSession = $controller->request->session()->read('Flash.flash');
        $expected = [[
            'message' => __d('core', 'Object has been moved'),
            'key'     => 'flash',
            'element' => 'Flash/success',
            'params'  => [],
        ]];

        $this->assertSame($expected, $flashSession);
    }

    public function testDown()
    {
        $request    = $this->_request;
        $request->addParams([
            'action' => 'down'
        ]);

        $controller = new MovesController($request);

        $entityId = 2;
        $entity = $controller->Moves->get($entityId);
        $this->assertSame($entityId, $entity->get('id'));
        $this->assertSame(2, $entity->get('lft'));
        $this->assertSame(3, $entity->get('rght'));

        $result = $controller->down($entityId);
        $entity = $controller->Moves->get($entityId);
        $this->assertSame($entityId, $entity->get('id'));
        $this->assertSame(4, $entity->get('lft'));
        $this->assertSame(5, $entity->get('rght'));

        $flashSession = $controller->request->session()->read('Flash.flash');
        $expected = [[
            'message' => __d('core', 'Object has been moved'),
            'key'     => 'flash',
            'element' => 'Flash/success',
            'params'  => [],
        ]];

        $this->assertSame($expected, $flashSession);
        $this->assertSame(['Location' => 'http://localhost/moves'], $result->header());
    }
}

/**
 * Class MovesTable
 *
 * @package Core\Test\TestCase\Controller\Component
 */
class MovesTable extends Table
{

    /**
     * Default schema
     *
     * @var array
     */
    protected $_schema = [
        'id'           => ['type' => 'integer'],
        'parent_id'    => ['type' => 'integer', 'null' => true],
        'name'         => ['type' => 'string'],
        'slug'         => ['type' => 'string'],
        'params'       => 'text',
        'lft'          => ['type' => 'integer'],
        'rght'         => ['type' => 'integer'],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->schema($this->_schema);
        parent::initialize($config);
        $this->table('moves');
    }
}

/**
 * Class MovesController
 *
 * @package Core\Test\TestCase\Controller\Component
 * @property MoveComponent $Move
 * @property MovesTable $Moves
 */
class MovesController extends AppController
{

    /**
     * Component uses.
     *
     * @var array
     */
    public $components = [
        'Core.Move'
    ];

    /**
     * Move up action.
     *
     * @param int|null $id
     * @param int $step
     * @return \Cake\Network\Response|null
     */
    public function up($id = null, $step = 1)
    {
        return $this->Move->up($this->Moves, $id, $step);
    }

    /**
     * Move down action.
     *
     * @param int|null $id
     * @param int $step
     * @return \Cake\Network\Response|null
     */
    public function down($id = null, $step = 1)
    {
        return $this->Move->down($this->Moves, $id, $step);
    }
}
