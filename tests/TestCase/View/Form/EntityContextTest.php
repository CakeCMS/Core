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

namespace Core\Test\TestCase\View\Form;

use Cake\Network\Request;
use Core\ORM\Entity\Entity;
use Core\TestSuite\TestCase;
use Core\View\Form\EntityContext;

/**
 * Class EntityContext
 *
 * @package Core\Test\TestCase\View\Form
 */
class EntityContextTest extends TestCase
{

    public function testVal()
    {
        $entity = new Entity([
            'title'  => 'Test entity',
            'body'   => 'Test custom body',
            'params' => [
                'test' => 'Test value'
            ],
        ]);

        $context = new EntityContext(new Request(), [
            'entity' => $entity,
        ]);

        $value = $context->val('params');
        $this->assertInstanceOf('JBZoo\Data\JSON', $value);

        $value = $context->val('params.test');
        $this->assertSame('Test value', $value);
    }
}

