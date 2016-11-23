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

use Cake\Network\Request;
use Cake\Routing\Router;
use Core\TestSuite\TestCase;
use Core\Controller\AppController;
use Cake\Routing\Route\DashedRoute;
use Core\Controller\Component\AppComponent;

/**
 * Class AppComponentTest
 *
 * @package Core\Test\TestCase\Controller\Component
 */
class AppComponentTest extends TestCase
{

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
                'controller' => 'ComponentApp',
                'action'     => 'form',
                'pass'       => [],
            ],
        ]);
    }

    public function testRedirect()
    {
        $request = $this->_request;
        $controller = new ComponentAppController($request);

        $this->assertSame([
            'Location' => 'http://localhost/component-app',
        ], $controller->form()->header());

        $request = $this->_request;
        $request->addParams([
            'action' => 'edit'
        ]);
        $request->data = ['action' => 'saveNew'];
        $controller = new ComponentAppController($request);

        $this->assertSame([
            'Location' => 'http://localhost/component-app/add'
        ], $controller->edit()->header());
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
                'action' => 'add',
                'controller' => 'ComponentApp',
            ]
        ]);
    }

    public function add()
    {
        return 'add action';
    }
}
