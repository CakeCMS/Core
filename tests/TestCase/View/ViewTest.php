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

use Core\Plugin;
use Core\View\AppView;
use Core\View\AjaxView;
use Core\TestSuite\TestCase;

/**
 * Class ViewTest
 *
 * @package Core\Test\TestCase
 */
class ViewTest extends TestCase
{

    /**
     * @var AppView
     */
    protected $View;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->View = new AppView();
        Plugin::load('Test');
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->View);
        Plugin::unload('Test');
    }

    public function testAjaxViewType()
    {
        $view = new AjaxView();
        $this->assertSame('ajax', $view->layout);
    }
    
    public function testPartial()
    {
        $actual = $this->View->partial('frontend');
        $this->assertSame('Frontend partial', $actual);

        $actual = $this->View->partial('Test.plugin');
        $this->assertSame('Plugin partial', $actual);

        $this->assertNull($this->View->partial('no-found'));
    }
}
