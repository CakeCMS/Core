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

namespace Core\Test\TestCase;

use Core\Plugin;
use Cake\Event\Event;
use Core\View\AppView;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Filesystem\Folder;
use Cake\TestSuite\TestCase;
use Core\Controller\AppController;

/**
 * Class PluginTest
 *
 * @package Core\Test
 */
class PluginTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        Plugin::load('Test', ['autoload' => true]);
    }

    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload('Test');
    }

    public function testGetManifestPath()
    {
        $actual = Plugin::getManifestPath('Core');
        $this->assertSame(Plugin::path('Core') . 'plugin.manifest.php', $actual);

        $actual = Plugin::getManifestPath('Test');
        $this->assertSame(Plugin::path('Test') . 'plugin.manifest.php', $actual);

        $actual = Plugin::getManifestPath('Site');
        $this->assertNull($actual);

        $actual = Plugin::getManifestPath('NoExist');
        $this->assertNull($actual);
    }

    public function testGetData()
    {
        $data = Plugin::getData('Test');
        $this->assertInstanceOf('JBZoo\Data\Data', $data);
        $this->assertTrue(is_array($data->get('meta')));

        $data = Plugin::getData('Test', 'meta');
        $this->assertInstanceOf('JBZoo\Data\Data', $data);
        $this->assertSame('Test', $data->get('name'));

        $data = Plugin::getData('NoExist');
        $this->assertInstanceOf('JBZoo\Data\Data', $data);
        $this->assertNull($data->get('name'));
    }

    public function testLoad()
    {
        Plugin::unload('Test');
        Plugin::load('Test', ['autoload' => true]);

        $this->assertTrue(Plugin::loaded('Test'));
        $locales = Configure::read('App.paths.locales');

        $before = count($locales);
        $this->assertTrue(in_array(Plugin::getLocalePath('Test'), $locales));

        Plugin::unload('Test');
        $locales = Configure::read('App.paths.locales');
        $after   = count($locales);

        $this->assertFalse(($before == $after));
        Plugin::load('Test', ['autoload' => true]);
    }

    public function testLoadList()
    {
        $Folder      = new Folder();
        $pluginsDir  = TEST_APP_DIR . 'plugins' . DS;
        $TestPlgPath = $pluginsDir . 'PluginTest';

        $Folder->create($TestPlgPath, 0777);
        new File($TestPlgPath . '/config/bootstrap.php', true);
        new File($TestPlgPath . '/config/routes.php', true);

        $NoRoutesPlgPath = $pluginsDir . 'NoRoutes';
        $Folder->create($NoRoutesPlgPath, 0777);
        new File($NoRoutesPlgPath . '/config/bootstrap.php', true);

        $NoBootstrapPlgPath = $pluginsDir . 'NoBootstrap';
        $Folder->create($NoBootstrapPlgPath, 0777);
        new File($NoBootstrapPlgPath . '/config/routes.php', true);

        $NoConfigPlgPath = $pluginsDir . 'NoConfig';
        $Folder->create($NoConfigPlgPath, 0777);

        Plugin::load('Migrations');
        Plugin::loadList([
            'NoConfig',
            'NoRoutes',
            'PluginTest',
            'Migrations',
            'NoBootstrap',
            'DebugKit',
        ]);

        $this->assertTrue(Plugin::loaded('NoBootstrap'));
        $this->assertTrue(Plugin::loaded('PluginTest'));
        $this->assertTrue(Plugin::loaded('NoRoutes'));
        $this->assertTrue(Plugin::loaded('NoConfig'));
        $this->assertTrue(Plugin::loaded('DebugKit'));
        $this->assertTrue(Plugin::loaded('Migrations'));

        $Folder->delete($TestPlgPath);
        $Folder->delete($NoRoutesPlgPath);
        $Folder->delete($NoConfigPlgPath);
        $Folder->delete($NoBootstrapPlgPath);

        Plugin::unload('NoConfig');
        Plugin::unload('NoRoutes');
        Plugin::unload('PluginTest');
        Plugin::unload('Migrations');
        Plugin::unload('NoBootstrap');
        Plugin::unload('DebugKit');
    }
    
    public function testManifestEventViewInitialize()
    {
        $view = new AppView();
        $helpers = $view->helpers()->loaded();

        $this->assertTrue(is_array($view->viewVars));
        $this->assertSame('View initialize', $view->viewVars['fromManifest']);

        $this->assertTrue(!empty($helpers));
        $this->assertTrue(is_array($helpers));
        $this->assertTrue(in_array('Url', $helpers));
        $this->assertTrue(in_array('Html', $helpers));
        $this->assertTrue(in_array('Form', $helpers));
        $this->assertTrue(in_array('Flash', $helpers));
        $this->assertTrue(in_array('Paginator', $helpers));
    }
    
    public function testManifestControllerBeforeRender()
    {
        $controller = new AppController();
        $event = new Event('Controller.beforeRender', $controller);

        $controller->beforeRender($event);
        $viewVars = $controller->viewVars;

        $this->assertTrue(is_array($viewVars));
        $this->assertSame('Controller.beforeRender', $viewVars['eventName']);
        $this->assertSame('App', $viewVars['controllerName']);

        $request  = new Request();
        $response = new Response(['type' => 'application/json']);

        $controller = new AppController($request, $response);
        $controller->beforeRender($event);
        $viewVars = $controller->viewVars;

        $this->assertSame(true, $viewVars['_serialize']);
    }

    public function testManifestControllerBeforeFilter()
    {
        $controller = new AppController();
        $event = new Event('Controller.beforeFilter', $controller);

        $controller->beforeFilter($event);
        $viewVars = $controller->viewVars;

        $this->assertTrue(is_array($viewVars));
        $this->assertSame('Controller.beforeFilter', $viewVars['eventName']);
        $this->assertSame('App', $viewVars['controllerName']);
    }

    public function testManifestControllerBeforeRedirect()
    {
        $request    = new Request();
        $response   = new Response(['type' => 'application/json']);
        $controller = new AppController($request, $response);
        $event      = new Event('Controller.beforeRedirect', $controller);

        $url = 'http://localhost';
        $controller->beforeRedirect($event, $url, $response);
        $viewVars = $controller->viewVars;

        $this->assertTrue(is_array($viewVars));
        $this->assertSame($url, $viewVars['url']);
        $this->assertSame('App', $viewVars['controllerName']);
        $this->assertSame('application/json', $viewVars['responseType']);
        $this->assertSame('Controller.beforeRedirect', $viewVars['eventName']);
    }

    public function testManifestEventControllerInitialize()
    {
        $controller = new AppController();
        $components = $controller->components()->loaded();

        $this->assertTrue(is_array($components));
        $this->assertTrue(!empty($components));
        $this->assertTrue(in_array('Flash', $components));
        $this->assertTrue(in_array('RequestHandler', $components));
    }

    public function testManifestEventControllerAfterFilter()
    {
        $controller = new AppController();
        $event      = new Event('Controller.afterFilter', $controller);
        $controller->afterFilter($event);
        $viewVars = $controller->viewVars;

        $this->assertSame('Controller.afterFilter', $viewVars['eventName']);
        $this->assertSame('App', $viewVars['controllerName']);
    }

    public function testManifestBeforeRenderFile()
    {
        $view  = new AppView();
        $file  = __FILE__;
        $event = new Event('View.beforeRenderFile', $view);
        $view->beforeRenderFile($event, $file);
        $viewVars = $view->viewVars;

        $this->assertTrue(is_array($viewVars));
        $this->assertSame('View.beforeRenderFile', $viewVars['eventName']);
        $this->assertSame($file, $viewVars['file']);
    }
    
    public function testManifestAfterRenderFile()
    {
        $view    = new AppView();
        $file    = __FILE__;
        $content = 'After render content';
        $event   = new Event('View.afterRenderFile', $view);
        $view->afterRenderFile($event, $file, $content);
        $viewVars = $view->viewVars;

        $this->assertTrue(is_array($viewVars));
        $this->assertSame('View.afterRenderFile', $viewVars['eventName']);
        $this->assertSame($file, $viewVars['file']);
        $this->assertSame($content, $viewVars['content']);
    }

    public function testManifestBeforeRender()
    {
        $view    = new AppView();
        $file    = __FILE__;
        $event   = new Event('View.beforeRender', $view);
        $view->beforeRender($event, $file);
        $viewVars = $view->viewVars;

        $this->assertTrue(is_array($viewVars));
        $this->assertSame('View.beforeRender', $viewVars['eventName']);
        $this->assertSame($file, $viewVars['file']);
    }

    public function testManifestAfterRender()
    {
        $view    = new AppView();
        $file    = __FILE__;
        $event   = new Event('View.afterRender', $view);
        $view->afterRender($event, $file);
        $viewVars = $view->viewVars;

        $this->assertTrue(is_array($viewVars));
        $this->assertSame('View.afterRender', $viewVars['eventName']);
        $this->assertSame($file, $viewVars['file']);
    }

    public function testManifestBeforeLayout()
    {
        $view    = new AppView();
        $file    = __FILE__;
        $event   = new Event('View.beforeLayout', $view);
        $view->beforeLayout($event, $file);
        $viewVars = $view->viewVars;

        $this->assertTrue(is_array($viewVars));
        $this->assertSame('View.beforeLayout', $viewVars['eventName']);
        $this->assertSame($file, $viewVars['file']);
    }

    public function testManifestAfterLayout()
    {
        $view    = new AppView();
        $file    = __FILE__;
        $event   = new Event('View.afterLayout', $view);
        $view->afterLayout($event, $file);
        $viewVars = $view->viewVars;

        $this->assertTrue(is_array($viewVars));
        $this->assertSame('View.afterLayout', $viewVars['eventName']);
        $this->assertSame($file, $viewVars['file']);
    }
}
