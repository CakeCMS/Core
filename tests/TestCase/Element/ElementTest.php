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

namespace Core\Test\TestCase\Element;

use JBZoo\Utils\FS;
use JBZoo\Utils\Str;
use Cake\Core\Configure;
use Core\Element\Element;
use Core\TestSuite\TestCase;
use Core\ORM\Entity\Element as ElementEntity;

/**
 * Class ElementTest
 *
 * @package Core\Test\TestCase\Element
 */
class ElementTest extends TestCase
{

    public function testClassName()
    {
        $element = new TestElement('Title', 'Item');
        $this->assertInstanceOf('Core\Test\TestCase\Element\TestElement', $element);
    }

    public function testGetPath()
    {
        $element = new TestElement('Title', 'Item');
        $path = $element->getPath();

        $this->assertNotFalse($path);

        $expected = FS::clean(WWW_ROOT . 'elements/Item/Title', '/');
        $this->assertSame($expected, $path);

        $element = new TestElement('Title', 'NotFound');
        $this->assertFalse($element->getPath());

        // TODO add custom element path to test
    }

    public function testLoadMeta()
    {
        $element = new TestElement('Title', 'Item');
        $meta = $element->loadMeta();

        $this->assertInstanceOf('JBZoo\Data\PHPArray', $meta);
        $this->assertSame('Item Title', $meta->find('meta.name'));
        $this->assertTrue($meta->find('meta.core'));
    }

    public function testGetMetaData()
    {
        $element = new TestElement('Title', 'Item');
        $this->assertSame('Item Title', $element->getMetaData('name'));
        $this->assertTrue($element->getMetaData('core'));
        $this->assertNull($element->getMetaData('not-found'));
        $this->assertFalse($element->getMetaData('not-found', false));
    }

    public function testIsCore()
    {
        $element = new TestElement('Title', 'Item');
        $this->assertTrue($element->isCore());

        $element = new TestElement('Custom', 'Item');
        $this->assertFalse($element->isCore());
    }

    public function testSetConfig()
    {
        $element = new TestElement('Title', 'Item');
        $this->assertNull($element->config);
        $element->setConfig([
            'id' => '_' . Str::low('Title'),
            'description' => '',
            'name' => $element->getName(),
        ]);

        $this->assertInstanceOf('JBZoo\Data\JSON', $element->config);
        $this->assertSame('_title', $element->config->get('id'));
        $this->assertSame('', $element->config->get('description'));
        $this->assertSame('Item Title', $element->config->get('name'));
    }

    public function testSetEntity()
    {
        $element = new TestElement('Title', 'Item');
        $this->assertNull($element->getEntity());

        $element->setEntity(new TestEntity());
        $this->assertInstanceOf('Core\Test\TestCase\Element\TestEntity', $element->getEntity());
    }
}

/**
 * Class TestElement
 *
 * @package Core\Test\TestCase\Element
 */
class TestElement extends Element
{
}

/**
 * Class TestEntity
 *
 * @package Core\Test\TestCase\Element
 */
class TestEntity extends ElementEntity
{
}
