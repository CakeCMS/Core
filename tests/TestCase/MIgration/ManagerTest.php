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
use Core\TestSuite\TestCase;
use Core\Migration\Manager as MigrateManager;

/**
 * Class ManagerTest
 *
 * @package Core\Test\TestCase\Migration
 */
class ManagerTest extends TestCase
{

    public $fixtures = ['plugin.core.phinxlog'];

    public function setUp()
    {
        parent::setUp();
        Plugin::load('Realty', ['autoload' => true]);
        Plugin::load('Migrate', ['autoload' => true]);
        Plugin::load('MigrateNoClass', ['autoload' => true]);
        Plugin::load('MigrateNoInstance', ['autoload' => true]);
    }

    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload('Realty');
        Plugin::unload('Migrate');
        Plugin::unload('MigrateNoClass');
        Plugin::unload('MigrateNoInstance');
    }
    
    public function testGetMigrations()
    {
        $migrate = new MigrateManager('Realty');
        $migrations = $migrate->getMigrations();
        self::assertTrue(is_array($migrations));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetMigrationsClassNoInstance()
    {
        $migrate = new MigrateManager('MigrateNoInstance');
        $migrate->getMigrations();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetMigrationsDuplicateMigration()
    {
        $migrate = new MigrateManager('Migrate');
        $migrate->getMigrations();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetMigrationsNoExistClass()
    {
        $migrate = new MigrateManager('MigrateNoClass');
        $migrate->getMigrations();
    }

    public function testMigrateUpSuccess()
    {
        $migrate = new MigrateManager('Realty');
        $output  = $migrate->migrateUp();
        self::assertTrue(is_array($output));
    }
}
