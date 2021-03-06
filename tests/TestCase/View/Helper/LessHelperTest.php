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

namespace Core\Test\TestCase\View\Helper;

use Core\Plugin;
use JBZoo\Utils\FS;
use Core\View\AppView;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Core\View\Helper\LessHelper;

/**
 * Class LessHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 * @method \Core\View\Helper\LessHelper _helper()
 */
class LessHelperTest extends HelperTestCase
{

    protected $_name = 'Less';
    protected $_plugin = 'Core';

    public function setUp()
    {
        parent::setUp();
        $this->loadPlugins(['Core', 'Test']);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->_clearCache();
    }

    public function testClassName()
    {
        self::assertInstanceOf('Core\View\Helper\LessHelper', $this->_helper());
    }

    public function testPluginLessProcess()
    {
        $url  = $this->_helper()->process('Test.custom.less', true);
        $full = $this->_getCacheFilePath($url);

        self::assertTrue(file_exists($full));
        self::assertRegExp('(http://localhost/test/img/cake.png)', file_get_contents($full));
    }

    public function testAppLessProcess()
    {
        $url  = $this->_helper()->process('styles.less', true);
        $full = $this->_getCacheFilePath($url);

        self::assertTrue(file_exists($full));
        self::assertRegExp('(http://localhost/img/cake-big.jpg)', file_get_contents($full));
    }

    public function testProcessNoDebug()
    {
        Configure::write('debug', false);

        $less = new LessHelper(new AppView());
        $url  = $less->process('styles.less', true);
        $full = $this->_getCacheFilePath($url);

        self::assertTrue(file_exists($full));

        $content = file_get_contents($full);
        $lines   = explode(PHP_EOL, $content);

        self::assertSame(3, count($lines));
        self::assertRegExp('/.*\.min\.css/', $url);

        Configure::write('debug', true);
    }

    public function testProcessTimeStamp()
    {
        Configure::write('Asset', ['timestamp' => true]);

        $url  = $this->_helper()->process('styles.less', true);
        $full = $this->_getCacheFilePath($url);

        self::assertTrue(file_exists($full));

        $content = file_get_contents($full);
        self::assertRegExp('/localhost\/img\/cake-big\.jpg\?[0-9]+/', $content);

        Configure::delete('Asset');
    }

    public function testProcessNoExistFile()
    {
        $url = $this->_helper()->process('not-found.less', true);
        self::assertNull($url);

        $url = $this->_helper()->process('Plugin.not-found.less', true);
        self::assertNull($url);
    }

    /**
     * @expectedException \JBZoo\Less\Exception
     */
    public function testProcessError()
    {
        $this->_helper()->process('error.less');
    }

    /**
     * @return void
     */
    protected function _clearCache()
    {
        $path = FS::clean(APP_ROOT . Configure::read('App.webroot') . '/' . Configure::read('App.cssBaseUrl') . 'cache');
        $folder = new Folder($path);
        $folder->delete();
    }

    /**
     * @param string $url
     * @return string
     */
    protected function _getCacheFilePath($url)
    {
        return FS::clean(APP_ROOT . Configure::read('App.webroot') . '/' . $url);
    }
}
