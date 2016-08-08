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
use Core\Theme;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\TestSuite\TestCase;

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

    /**
     * {@inheritdoc}
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->_folder = new Folder();
        $this->_paths  = App::path('Plugin');
    }

    public function testNotFound()
    {
        $this->assertNull(Theme::get());
    }

    public function testNotFoundTheme()
    {
        $actual = Theme::get();
        $this->assertNull($actual);
    }

    public function testCustomTheme()
    {
        $themeName = 'Realty';
        Configure::write('Theme.site', $themeName);
        $actual = Theme::get();

        $this->assertSame($themeName, $actual);
        $this->assertTrue(Plugin::loaded($themeName));
        Plugin::unload($themeName);
    }

    public function testNotFoundManifestParam()
    {
        $themeName = 'Test';
        Configure::write('Theme.site', $themeName);
        $actual = Theme::get();
        $this->assertNull($actual);
    }
}
