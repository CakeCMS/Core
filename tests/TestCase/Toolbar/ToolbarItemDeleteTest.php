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

namespace Core\Test\TestCase\Toolbar;

use Core\Utility\Toolbar;
use Core\TestSuite\TestCase;

/**
 * Class ToolbarItemDeleteTest
 *
 * @package Core\Test\TestCase\Toolbar
 */
class ToolbarItemDeleteTest extends TestCase
{

    public function testFetchDeleteItem()
    {
        $toolbar = Toolbar::getInstance(__FUNCTION__);
        $toolbar->appendButton('Core.delete', 'Delete action', 'delete');

        $this->assertHtml([
            'div' => ['id' => 'test-fetch-delete-item-core-delete', 'class' => 'item-wrapper tb-item-1 first last'],
                'button' => [
                    'class' => 'jsProcessDelete ck-button waves-effect waves-light btn red lighten-2',
                    'data-action' => 'delete',
                    'type' => 'submit',
                ],
                    'i' => ['class' => 'ck-icon fa fa-trash'], '/i',
                    'Delete action',
                '/button',
            '/div',
        ], $toolbar->render());
    }
}
