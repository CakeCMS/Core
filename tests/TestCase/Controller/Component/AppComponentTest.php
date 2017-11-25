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

use Core\Plugin;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\Http\ServerRequest;
use Core\TestSuite\TestCase;
use Core\Controller\AppController;
use Cake\Routing\Route\DashedRoute;
use Test\Controller\MetadataController;
use Core\Controller\Component\AppComponent;

/**
 * Class AppComponentTest
 *
 * @package Core\Test\TestCase\Controller\Component
 */
class AppComponentTest extends TestCase
{

    /**
     * @var array
     */
    public $fixtures = ['plugin.core.pages'];

    /**
     * @var ServerRequest
     */
    protected $_request;

    public function setUp()
    {
        parent::setUp();

        Plugin::load('Test', ['autoload' => true, 'routes' => true]);
        Plugin::routes('Test');

        Router::scope('/', function ($routes) {
            /** @var $routes \Cake\Routing\RouteBuilder */
            $routes->fallbacks(DashedRoute::class);
        });

        $this->_request = new ServerRequest([
            'params' => [
                'prefix'     => false,
                'plugin'     => false,
                'controller' => 'ComponentApp',
                'action'     => 'form',
                'pass'       => [],
            ],
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload('Test');
    }

    public function testRedirect()
    {
        $request    = $this->_request;
        $controller = new ComponentAppController($request);

        self::assertSame('http://localhost/component-app', $controller->form()->getHeaderLine('Location'));
        self::assertSame('text/html; charset=UTF-8', $controller->form()->getHeaderLine('Content-Type'));

        $request = $this->_request;
        $request->addParams([
            'action' => 'edit'
        ]);

        $request->data = ['action' => 'saveNew'];

        $controller = new ComponentAppController($request);

        self::assertSame('http://localhost/component-app/add', $controller->edit()->getHeaderLine('Location'));
        self::assertSame('text/html; charset=UTF-8', $controller->edit()->getHeaderLine('Content-Type'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testToggleFieldFail()
    {
        $request = new ServerRequest([
            'params' => [
                'plugin'      => 'Test',
                'controller'  => 'Metadata',
                'action'      => 'toggleStatus',
                'pass'        => [],
            ]
        ]);

        $entityId   = 4;
        $controller = new MetadataController($request);
        $controller->toggle($entityId, 1);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testToggleFieldBadRequest()
    {
        $request = new ServerRequest([
            'params' => [
                'plugin'      => 'Test',
                'controller'  => 'Metadata',
                'action'      => 'toggleStatus',
                'pass'        => [],
            ],
            'environment' => [
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ]
        ]);

        $controller = new MetadataController($request);
        $controller->toggle('', '');
    }

    public function testToggleFieldSuccess()
    {
        $request = new ServerRequest([
            'params' => [
                'plugin'      => 'Test',
                'controller'  => 'Metadata',
                'action'      => 'toggleStatus',
                'pass'        => [],
            ],
            'environment' => [
                'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'
            ]
        ]);

        $entityId   = 1;
        $table      = TableRegistry::get('Test.Pages');
        $entity     = $table->get($entityId);
        $controller = new MetadataController($request);

        self::assertSame(1, $entity->get('status'));

        $controller->toggle($entity->get('id'), $entity->get('status'));
        self::assertSame(0, $table->get($entityId)->get('status'));

        $view   = $controller->createView('Core\View\AppView');
        $actual = json_decode($view->render(), true);

        self::assertCount(4, $actual);
        self::assertSame(1, $actual['id']);
        self::assertFalse($actual['status']);
        self::assertTrue(array_key_exists('id', $actual));
        self::assertTrue(array_key_exists('url', $actual));
        self::assertTrue(array_key_exists('output', $actual));
        self::assertTrue(array_key_exists('status', $actual));
        self::assertSame('/test/metadata/toggle-status/1/0', $actual['url']);
    }
}

/**
 * Class ComponentAppController
 *
 * @package Core\Test\TestCase\Controller\Component
 * @property AppComponent $App
 */
class ComponentAppController extends AppController
{

    public $components = ['Core.App'];

    public function form()
    {
        return $this->App->redirect();
    }

    public function edit()
    {
        return $this->App->redirect([
            'saveNew' => [
                'action'     => 'add',
                'controller' => 'ComponentApp',
            ]
        ]);
    }

    public function add()
    {
        return 'add action';
    }
}
