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

use Core\ORM\Table;
use Cake\Http\Response;
use Cake\Routing\Router;
use Cake\Network\Session;
use Cake\Http\ServerRequest;
use Core\TestSuite\TestCase;
use Core\Controller\AppController;
use Cake\Routing\Route\DashedRoute;
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
     * @var ServerRequest
     */
    protected $_request;

    public function setUp()
    {
        parent::setUp();

        Router::scope('/', function ($routes) {
            /** @var $routes \Cake\Routing\RouteBuilder */
            $routes->fallbacks(DashedRoute::class);
        });

        $this->_request = new ServerRequest([
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

    public function testDown()
    {
        $request    = $this->_request;
        $request->addParams([
            'action' => 'down'
        ]);

        $controller = new MovesController($request);

        $entityId = 2;
        $entity = $controller->Moves->get($entityId);

        self::assertSame($entityId, $entity->get('id'));
        self::assertSame(2, $entity->get('lft'));
        self::assertSame(3, $entity->get('rght'));

        $response = $controller->down($entityId);
        $entity   = $controller->Moves->get($entityId);

        self::assertSame($entityId, $entity->get('id'));
        self::assertSame(4, $entity->get('lft'));
        self::assertSame(5, $entity->get('rght'));

        $flashSession = $controller->request->session()->read('Flash.flash');
        $expected = [[
            'message' => __d('core', 'Object has been moved'),
            'key'     => 'flash',
            'element' => 'Flash/success',
            'params'  => [],
        ]];

        self::assertSame($expected, $flashSession);
        self::_assertRedirect($response);
    }

    public function testUpFail()
    {
        $request = $this->_request;
        $controller = new MovesController($request);

        $entityId = 3;
        $response = $controller->up($entityId, 'text');

        $flashSession = $controller->request->session()->read('Flash.flash');
        $expected = [[
            'message' => __d('core', 'Object could not been moved'),
            'key'     => 'flash',
            'element' => 'Flash/error',
            'params'  => [],
        ]];

        self::assertSame($expected, $flashSession);
        self::_assertRedirect($response);
    }

    public function testUpSuccess()
    {
        $request    = $this->_request;
        $controller = new MovesController($request);

        $entityId = 3;
        $entity   = $controller->Moves->get($entityId);

        self::assertSame('Admin', $entity->get('name'));
        self::assertSame($entityId, $entity->get('id'));
        self::assertSame(1, $entity->get('parent_id'));
        self::assertSame(4, $entity->get('lft'));
        self::assertSame(5, $entity->get('rght'));

        $response = $controller->up($entityId);
        $entity   = $controller->Moves->get($entityId);

        self::_assertRedirect($response);
        self::assertSame('Admin', $entity->get('name'));
        self::assertSame($entityId, $entity->get('id'));
        self::assertSame(1, $entity->get('parent_id'));
        self::assertSame(2, $entity->get('lft'));
        self::assertSame(3, $entity->get('rght'));

        $flashSession = $controller->request->session()->read('Flash.flash');
        $expected = [[
            'message' => __d('core', 'Object has been moved'),
            'key'     => 'flash',
            'element' => 'Flash/success',
            'params'  => [],
        ]];

        self::assertSame($expected, $flashSession);
    }

    /**
     * Check request.
     *
     * @param Response $response
     * @return void
     */
    protected static function _assertRedirect(Response $response)
    {
        self::assertSame('http://localhost/moves', $response->getHeaderLine('Location'));
        self::assertSame('text/html; charset=UTF-8', $response->getHeaderLine('Content-Type'));
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
     * Move down action.
     *
     * @param int|null $id
     * @param int $step
     * @return \Cake\Http\Response|null
     */
    public function down($id = null, $step = 1)
    {
        return $this->Move->down($this->Moves, $id, $step);
    }

    /**
     * Move up action.
     *
     * @param int|null $id
     * @param int $step
     * @return \Cake\Http\Response|null
     */
    public function up($id = null, $step = 1)
    {
        return $this->Move->up($this->Moves, $id, $step);
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
     * Default table schema.
     *
     * @var array
     */
    protected static $_tableSchema = [
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
     * Initialize a table instance. Called after the constructor.
     *
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->setSchema(self::$_tableSchema);
        parent::initialize($config);
        $this->setTable('moves');
    }
}
