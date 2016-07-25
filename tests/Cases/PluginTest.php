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

namespace Core\Test\Cases;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Core\Plugin;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

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
    }
}
