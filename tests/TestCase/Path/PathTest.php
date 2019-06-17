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

namespace Core\Test\TestCase\Path;

use Core\Cms;
use Core\Path\Path;
use Test\Cases\TestCase;

/**
 * Class PathTest
 * 
 * @package Core\Test\TestCase\Path
 */
class PathTest extends TestCase
{

    /**
     * @var Path
     */
    protected $_path;

    /**
     * @throws  \JBZoo\Path\Exception
     */
    public function setUp()
    {
        parent::setUp();
        $this->_path = Path::getInstance();
        $this->_path->set('plugins', TEST_APP_DIR . 'plugins');
    }

    public function testDirs()
    {
        $dirs = $this->_path->dirs('plugins:Test/src');

        self::assertSame([
            'Controller',
            'Event',
            'Helper',
            'Model',
            'Template',
            'Toolbar'
        ], $dirs);
    }

    public function testLsDir()
    {
        $dirs = $this->_path->ls('plugins:Test/src', Path::LS_MODE_DIR);
        self::assertSame([
            'Controller',
            'Event',
            'Helper',
            'Model',
            'Template',
            'Toolbar'
        ], $dirs);

        $dirs = $this->_path->ls('plugins:Test/webroot', Path::LS_MODE_DIR, true);
        self::assertSame([
            'css',
            'img',
            'js',
            'js/widget',
            'less',
            'path',
            'path/dir'
        ], $dirs);

        $dirs = $this->_path->ls('plugins:Test/webroot', Path::LS_MODE_DIR, true, '/^path/');
        self::assertSame(['path'], $dirs);
    }

    public function testLsFile()
    {
        $files = $this->_path->ls('plugins:Realty');
        self::assertSame(['plugin.manifest.php'], $files);

        $files = $this->_path->ls('plugins:Test/webroot/path', Path::LS_MODE_FILE, true);
        self::assertSame([
            'dir/file-3.css',
            'file-1.css',
            'file-2.css'
        ], $files);

        $files = $this->_path->ls('plugins:Test/webroot/path', Path::LS_MODE_FILE, true, '/3\.css/');
        self::assertSame([
            'dir/file-3.css'
        ], $files);
    }
}
