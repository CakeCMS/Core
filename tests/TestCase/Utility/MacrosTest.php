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

namespace Core\Test\TestCase\Utility;

use Cake\Routing\Router;
use Core\Utility\Macros;
use Test\Cases\TestCase;
use Core\ORM\Entity\Entity;

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
        self::assertSame(['base_url' => Router::fullBaseUrl()], $macros->get());

        $macros
            ->set('test_1', 'Test 1')
            ->set('test_2', 'Test 2')
            ->set('test_3', 'Test 3');

        self::assertSame([
            'test_3'    => 'Test 3',
            'test_2'    => 'Test 2',
            'test_1'    => 'Test 1',
            'base_url'  => Router::fullBaseUrl()
        ], $macros->get());

        self::assertSame('Test 3', $macros->get('test_3'));

        $entity = new Entity(['status' => 'Publish']);
        $macros = new Macros($entity);

        self::assertSame([
            'base_url' => 'http://localhost',
            'status'   => 'Publish'
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
        self::assertSame($expected, $actual);

        $entity = new TestEntity(['name' => 'Piter']);
        $macros = new Macros($entity);
        $actual = $macros->text('Hello, {name}. It you url - {activation_url}');
        self::assertSame('Hello, Piter. It you url - /activation/user/1', $actual);
    }
}

/**
 * Class TestEntity
 *
 * @package Core\Test\TestCase\Utility
 */
class TestEntity extends Entity
{

    /**
     * List of computed or virtual fields that **should** be included in JSON or array
     * representations of this Entity. If a field is present in both _hidden and _virtual
     * the field will **not** be in the array/json versions of the entity.
     *
     * @var array
     */
    protected $_virtual = [
        'activation_url'
    ];

    /**
     * Set virtual field activation_url.
     *
     * @return string
     */
    protected function _getActivationUrl()
    {
        return '/activation/user/1';
    }
}