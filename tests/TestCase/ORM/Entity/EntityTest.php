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

namespace Core\Test\TestCase\ORM\Entity;

use Core\ORM\Entity\Entity;
use Core\TestSuite\TestCase;

/**
 * Class EntityTest
 *
 * @package Core\Test\TestCase\ORM\Entity
 */
class EntityTest extends TestCase
{

    public function testClassName()
    {
        $entity = new Entity();
        $this->assertInstanceOf('Core\ORM\Entity\Entity', $entity);
    }

    public function testParamsProperty()
    {
        $entity = new Entity();
        $this->assertInstanceOf('JBZoo\Data\JSON', $entity->params);

        $entity = new Entity([
            'params' => [
                'key-1'  => 'value-1',
                'custom' => 'is test',
            ]
        ]);

        $this->assertInstanceOf('JBZoo\Data\JSON', $entity->params);
        $this->assertInstanceOf('JBZoo\Data\JSON', $entity->get('params'));
        $this->assertSame('value-1', $entity->params->get('key-1'));
        $this->assertSame('is test', $entity->params->get('custom'));
    }
}
