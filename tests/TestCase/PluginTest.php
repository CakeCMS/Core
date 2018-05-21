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

namespace Core\Test\TestCase;

use Core\Plugin;
use Cake\Event\Event;
use Core\View\AppView;
use Cake\Http\Response;
use Cake\Core\Configure;
use Test\Cases\TestCase;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Http\ServerRequest;
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
        self::assertSame(Plugin::path('Core') . 'plugin.manifest.php', $actual);

        $actual = Plugin::getManifestPath('Test');
        self::assertSame(Plugin::path('Test') . 'plugin.manifest.php', $actual);

        $actual = Plugin::getManifestPath('Site');
        self::assertNull($actual);

        $actual = Plugin::getManifestPath('NoExist');
        self::assertNull($actual);
    }

    public function testGetData()
    {
        $data = Plugin::getData('Test');
        self::assertInstanceOf('JBZoo\Data\Data', $data);
        self::assertTrue(is_array($data->get('meta')));

        $data = Plugin::getData('Test', 'meta');
        self::assertInstanceOf('JBZoo\Data\Data', $data);
        self::assertSame('Test', $data->get('name'));

        $data = Plugin::getData('NoExist');
        self::assertInstanceOf('JBZoo\Data\Data', $data);
        self::assertNull($data->get('name'));
    }

    public function testLoad()
    {
        Plugin::unload('Test');
        Plugin::load('Test', ['autoload' => true]);

        self::assertTrue(Plugin::loaded('Test'));
        $locales = Configure::read('App.paths.locales');

        $before = count($locales);
        self::assertTrue(in_array(Plugin::getLocalePath('Test'), $locales));

        Plugin::unload('Test');
        $locales = Configure::read('App.paths.locales');
        $after   = count($locales);

        self::assertFalse(($before == $after));
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
            'NoBootstrap'
        ]);

        self::assertTrue(Plugin::loaded('NoBootstrap'));
        self::assertTrue(Plugin::loaded('PluginTest'));
        self::assertTrue(Plugin::loaded('NoRoutes'));
        self::assertTrue(Plugin::loaded('NoConfig'));
        self::assertTrue(Plugin::loaded('Migrations'));

        $Folder->delete($TestPlgPath);
        $Folder->delete($NoRoutesPlgPath);
        $Folder->delete($NoConfigPlgPath);
        $Folder->delete($NoBootstrapPlgPath);

        Plugin::unload('NoConfig');
        Plugin::unload('NoRoutes');
        Plugin::unload('PluginTest');
        Plugin::unload('Migrations');
        Plugin::unload('NoBootstrap');
    }
    
    public function testManifestEventViewInitialize()
    {
        $view = new AppView();
        $helpers = $view->helpers()->loaded();

        self::assertTrue(is_array($view->viewVars));
        self::assertSame('View initialize', $view->viewVars['fromManifest']);

        self::assertTrue(!empty($helpers));
        self::assertTrue(is_array($helpers));
        self::assertTrue(in_array('Url', $helpers));
        self::assertTrue(in_array('Html', $helpers));
        self::assertTrue(in_array('Form', $helpers));
        self::assertTrue(in_array('Flash', $helpers));
        self::assertTrue(in_array('Paginator', $helpers));
    }
    
    public function testManifestControllerBeforeRender()
    {
        $controller = new AppController();
        $event = new Event('Controller.beforeRender', $controller);

        $controller->beforeRender($event);
        $viewVars = $controller->viewVars;

        self::assertTrue(is_array($viewVars));
        self::assertSame('Controller.beforeRender', $viewVars['eventName']);
        self::assertSame('App', $viewVars['controllerName']);

        $request  = new ServerRequest();
        $response = new Response(['type' => 'application/json']);

        $controller = new AppController($request, $response);
        $controller->beforeRender($event);
        $viewVars = $controller->viewVars;

        self::assertSame(true, $viewVars['_serialize']);
    }

    public function testManifestControllerBeforeFilter()
    {
        $controller = new AppController();
        $event = new Event('Controller.beforeFilter', $controller);

        $controller->beforeFilter($event);
        $viewVars = $controller->viewVars;

        self::assertTrue(is_array($viewVars));
        self::assertSame('Controller.beforeFilter', $viewVars['eventName']);
        self::assertSame('App', $viewVars['controllerName']);
    }

    public function testManifestControllerBeforeRedirect()
    {
        $request    = new ServerRequest();
        $response   = new Response(['type' => 'application/json']);
        $controller = new AppController($request, $response);
        $event      = new Event('Controller.beforeRedirect', $controller);

        $url = 'http://localhost';
        $controller->beforeRedirect($event, $url, $response);
        $viewVars = $controller->viewVars;

        self::assertTrue(is_array($viewVars));
        self::assertSame($url, $viewVars['url']);
        self::assertSame('App', $viewVars['controllerName']);
        self::assertSame('application/json', $viewVars['responseType']);
        self::assertSame('Controller.beforeRedirect', $viewVars['eventName']);
    }

    public function testManifestEventControllerInitialize()
    {
        $controller = new AppController();
        $components = $controller->components()->loaded();

        self::assertTrue(is_array($components));
        self::assertTrue(!empty($components));
        self::assertTrue(in_array('Flash', $components));
        self::assertTrue(in_array('RequestHandler', $components));
    }

    public function testManifestEventControllerAfterFilter()
    {
        $controller = new AppController();
        $event      = new Event('Controller.afterFilter', $controller);
        $controller->afterFilter($event);
        $viewVars = $controller->viewVars;

        self::assertSame('Controller.afterFilter', $viewVars['eventName']);
        self::assertSame('App', $viewVars['controllerName']);
    }

    public function testManifestBeforeRenderFile()
    {
        $view  = $this->_getView();
        $file  = __FILE__;
        $event = new Event('View.beforeRenderFile', $view);
        $view->Document->beforeRenderFile($event, $file);
        $viewVars = $view->viewVars;

        self::assertTrue(is_array($viewVars));
        self::assertSame('View.beforeRenderFile', $viewVars['eventName']);
        self::assertSame($file, $viewVars['file']);
    }
    
    public function testManifestAfterRenderFile()
    {
        $view    = $this->_getView();
        $file    = __FILE__;
        $content = 'After render content';
        $event   = new Event('View.afterRenderFile', $view);
        $view->Document->afterRenderFile($event, $file, $content);
        $viewVars = $view->viewVars;

        self::assertTrue(is_array($viewVars));
        self::assertSame('View.afterRenderFile', $viewVars['eventName']);
        self::assertSame($file, $viewVars['file']);
        self::assertSame($content, $viewVars['content']);
    }

    public function testManifestBeforeRender()
    {
        $view    = $this->_getView();
        $file    = __FILE__;
        $event   = new Event('View.beforeRender', $view);
        $view->Document->beforeRender($event, $file);
        $viewVars = $view->viewVars;

        self::assertTrue(is_array($viewVars));
        self::assertSame('View.beforeRender', $viewVars['eventName']);
        self::assertSame($file, $viewVars['file']);
    }

    public function testManifestAfterRender()
    {
        $view    = $this->_getView();
        $file    = __FILE__;
        $event   = new Event('View.afterRender', $view);
        $view->Document->afterRender($event, $file);
        $viewVars = $view->viewVars;

        self::assertTrue(is_array($viewVars));
        self::assertSame('View.afterRender', $viewVars['eventName']);
        self::assertSame($file, $viewVars['file']);
    }

    public function testManifestBeforeLayout()
    {
        $view    = $this->_getView();
        $file    = __FILE__;
        $event   = new Event('View.beforeLayout', $view);
        $view->Document->beforeLayout($event, $file);
        $viewVars = $view->viewVars;

        self::assertTrue(is_array($viewVars));
        self::assertSame('View.beforeLayout', $viewVars['eventName']);
        self::assertSame($file, $viewVars['file']);
    }

    /**
     * Get app view object.
     *
     * @return AppView
     */
    protected function _getView()
    {
        return new AppView();
    }
}
