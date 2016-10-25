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

use Core\Element\Manager;
use Core\TestSuite\TestCase;
use Core\ORM\Entity\Element as ElementEntity;

/**
 * Class ManagerTest
 *
 * @package Core\Test\TestCase\Element
 */
class ManagerTest extends TestCase
{

    /**
     * @expectedException \Core\Element\Exception\ElementException
     */
    public function testCreateTypeIsEmpty()
    {
        $this->_getManager()->create('');
    }

    /**
     * @expectedException \Core\Element\Exception\ElementException
     */
    public function testCreateGroupIsEmpty()
    {
        $this->_getManager()->create('Custom', '');
    }

    /**
     * @expectedException \Core\Element\Exception\ElementException
     */
    public function testNotFoundElementClass()
    {
        $this->_getManager()->create('NotFound');
    }

    public function testSuccessCreateElement()
    {
        $element = $this->_getManager()->create('Title', 'Item', [], new CustomEntity());

        $this->assertSame('_title', $element->id);
        $this->assertSame('Item', $element->config->get('group'));
        $this->assertSame('Title', $element->config->get('type'));
        $this->assertSame('Item Title', $element->config->get('name'));

        $this->assertInstanceOf('JBZoo\Data\JSON', $element->config);
        $this->assertInstanceOf('Elements\Item\TitleElement', $element);
        $this->assertInstanceOf('JBZoo\Data\PHPArray', $element->loadMeta());
        $this->assertInstanceOf('Core\Test\TestCase\Element\CustomEntity', $element->getEntity());
    }

    /**
     * @return Manager
     */
    protected function _getManager()
    {
        return new Manager();
    }
}

/**
 * Class CustomEntity
 *
 * @package Core\Test\TestCase\Element
 */
class CustomEntity extends ElementEntity
{
}