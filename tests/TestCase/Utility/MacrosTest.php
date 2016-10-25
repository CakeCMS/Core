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

namespace Core\Test\TestCase\Utility;

use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Core\ORM\Entity\Entity;
use Core\Utility\Macros;

/**
 * Class MacrosTest
 *
 * @package Core\Test\TestCase\Utility
 */
class MacrosTest extends TestCase
{

    public function testSetGet()
    {
        $macros = new Macros();
        $this->assertSame(['base_url' => Router::fullBaseUrl()], $macros->get());

        $macros
            ->set('test_1', 'Test 1')
            ->set('test_2', 'Test 2')
            ->set('test_3', 'Test 3');

        $this->assertSame([
            'test_3' => 'Test 3',
            'test_2' => 'Test 2',
            'test_1' => 'Test 1',
            'base_url' => Router::fullBaseUrl()
        ], $macros->get());

        $this->assertSame('Test 3', $macros->get('test_3'));

        $entity = new Entity(['status' => 'Publish']);
        $macros = new Macros($entity);
        $this->assertSame([
            'base_url' => 'http://localhost',
            'status'   => 'Publish',
        ], $macros->get());
    }

    public function testText()
    {
        $macros = new Macros([
            'name' => 'Sergey',
            'age'  => 26,
        ]);

        $expected = 'Hi, i\'m Sergey and me 26 years';
        $actual   = $macros->text('Hi, i\'m {name} and me {age} years');
        $this->assertSame($expected, $actual);
    }
}
