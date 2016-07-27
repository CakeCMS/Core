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

namespace Core\Test\TestCase;

use Core\View\AjaxView;
use Cake\TestSuite\TestCase;

/**
 * Class ViewTest
 *
 * @package Core\Test\TestCase
 */
class ViewTest extends TestCase
{

    public function testAjaxViewType()
    {
        $view = new AjaxView();
        $this->assertSame('ajax', $view->layout);
    }
}
