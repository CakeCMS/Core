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

use Core\Cms;
use Cake\Core\Configure;
use Core\TestSuite\TestCase;

/**
 * Class CmsTest
 *
 * @package Core\Test\Cases
 */
class CmsTest extends TestCase
{

    public function testMergeConfig()
    {
        Configure::write('Test.key', 'value-1');

        Cms::mergeConfig('Test.key', 'value-2');
        $expected = ['value-1', 'value-2'];
        self::assertSame($expected, Configure::read('Test.key'));

        Cms::mergeConfig('Test.key', 'config_value');
        $expected = ['value-1', 'value-2', 'config_value'];
        self::assertSame($expected, Configure::read('Test.key'));

        Cms::mergeConfig('Test.key', true);
        $expected = ['value-1', 'value-2', 'config_value', true];
        self::assertSame($expected, Configure::read('Test.key'));

        Cms::mergeConfig('Test.key', false);
        $expected = ['value-1', 'value-2', 'config_value', true, false];
        self::assertSame($expected, Configure::read('Test.key'));

        Cms::mergeConfig('Test.key', ['array-value-1', 'array-value-2']);
        $expected = ['value-1', 'value-2', 'config_value', true, false, 'array-value-1', 'array-value-2'];
        self::assertSame($expected, Configure::read('Test.key'));
    }

    public function testInitializeCms()
    {
        $app = Cms::getInstance();
        self::assertInstanceOf('Core\Cms', $app);
        self::assertInstanceOf('Core\Path\Path', $app['path']);
        self::assertInstanceOf('Core\Helper\Manager', $app['helper']);

        self::assertNotEmpty($app['path']->getPaths('locales'));
        self::assertNotEmpty($app['path']->getPaths('plugins'));
        self::assertNotEmpty($app['path']->getPaths('webroot'));
        self::assertNotEmpty($app['path']->getPaths('templates'));
    }
}
