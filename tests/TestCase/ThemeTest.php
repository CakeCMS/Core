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

use Core\Theme;
use Core\Plugin;
use Cake\Core\App;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Test\Cases\TestCase;
use Cake\Filesystem\Folder;

/**
 * Class ThemeTest
 *
 * @package Core\Test\TestCase
 */
class ThemeTest extends TestCase
{

    /**
     * @var Folder
     */
    protected $_folder;

    /**
     * @var array
     */
    protected $_paths;

    public function setUp()
    {
        parent::setUp();
        $this->_folder = new Folder();
        $this->_paths  = App::path('Plugin');
    }

    public function tearDown()
    {
        parent::tearDown();
        Cache::drop('test_cached');
    }

    public function testNotFound()
    {
        self::assertNull(Theme::setup());
    }

    public function testNotFoundTheme()
    {
        $actual = Theme::setup();
        self::assertNull($actual);
    }

    public function testCustomTheme()
    {
        Plugin::load('Realty');
        $themeName = 'Realty';
        Configure::write('Theme.site', $themeName);
        $actual = Theme::setup();

        self::assertSame($themeName, $actual);
        Plugin::unload('Realty');
    }

    public function testNotFoundManifestParam()
    {
        $themeName = 'Test';
        Configure::write('Theme.site', $themeName);
        $actual = Theme::setup();
        self::assertNull($actual);
    }
}
