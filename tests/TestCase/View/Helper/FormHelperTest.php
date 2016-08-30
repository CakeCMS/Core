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

namespace Core\Test\TestCase\View\Helper;

use Core\Plugin;
use Cake\Cache\Cache;
use Core\View\AppView;
use Cake\Core\Configure;
use Cake\Network\Request;
use Core\View\Helper\FormHelper;

/**
 * Class FormHelperTest
 *
 * @package Core\Test\TestCase\View\Helper
 * @method \Core\View\Helper\FormHelper _helper()
 */
class FormHelperTest extends HelperTestCase
{

    protected $_name = 'Form';
    protected $_plugin = 'Core';

    public function setUp()
    {
        parent::setUp();
        Plugin::loadList(['Test']);
        Plugin::routes('Test');
    }

    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload('Test');
        Cache::drop('test_cached');
    }

    public function testClassName()
    {
        $this->assertInstanceOf('Core\View\Helper\FormHelper', $this->_helper());
    }

    public function testCreateForm()
    {
        $expected = [
            'form' => ['method' => 'post', 'accept-charset' => 'utf-8', 'class' => 'ck-form', 'action' => '/'],
                'div' => ['style' => 'display:none;'],
                    'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
                '/div',
        ];

        $this->assertHtml($expected, $this->_helper()->create(false));
        $this->assertSame('</form>', $this->_helper()->end());

        $expected = [
            'form' => ['method' => 'post', 'accept-charset' => 'utf-8', 'class' => 'ck-form jsForm', 'action' => '/'],
                'div' => ['style' => 'display:none;'],
                    'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
                '/div',
        ];

        $helper = $this->_helper();
        $this->assertHtml($expected, $helper->create(false, ['jsForm' => true]));
        $this->assertSame(
            '<input type="hidden" name="action" class="jsFormAction" value=""/></form>',
            $helper->end()
        );
    }

    public function testCreateProcessForm()
    {
        $Request = new Request([
            'params' => [
                'plugin'     => 'Test',
                'controller' => 'Event',
                'action'     => 'index',
                'pass'       => [],
            ],
        ]);

        $View = new AppView($Request);
        $Form = new FormHelper($View);

        $expected = [
            'form' => [
                'method'         => 'post',
                'accept-charset' => 'utf-8',
                'class'          => 'ck-form jsForm',
                'action'         => '/test/event/process',
            ],
                'div' => ['style' => 'display:none;'],
                    'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
                '/div',
        ];

        $this->assertHtml($expected, $Form->create(false, ['process' => true]));
        $this->assertSame('<input type="hidden" name="action" class="jsFormAction" value=""/></form>', $Form->end());
    }
}
