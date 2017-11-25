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

namespace Core\Test\TestCase\Migration;

use Core\Plugin;
use JBZoo\Utils\FS;
use Core\TestSuite\TestCase;
use Core\Migration\Migration;

/**
 * Class MigrationTest
 *
 * @package Core\Test\TestCase\Migration
 */
class MigrationTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        Plugin::load('Realty', ['autoload' => true]);
    }

    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload('Realty');
    }

    /**
     * @expectedException \Cake\Core\Exception\MissingPluginException
     */
    public function testFailGetPath()
    {
        Migration::getPath('NoExits');
    }

    public function testGetData()
    {
        $data = Migration::getData('Realty');
        self::assertTrue(is_array($data));
    }

    public function testGetPath()
    {
        $path = Migration::getPath('Realty');
        $plgMigrationPath = FS::clean(Plugin::path('Realty') . '/config/Migrations', '/');
        self::assertSame($plgMigrationPath, $path);
    }
}
