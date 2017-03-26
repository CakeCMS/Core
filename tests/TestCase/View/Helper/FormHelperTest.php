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
use Core\ORM\Table;
use Cake\Cache\Cache;
use Core\View\AppView;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Core\ORM\Entity\Entity;
use Cake\Http\ServerRequest;
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
        self::assertInstanceOf('Core\View\Helper\FormHelper', $this->_helper());
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
        self::assertSame('</form>', $this->_helper()->end());

        $expected = [
            'form' => ['method' => 'post', 'accept-charset' => 'utf-8', 'class' => 'ck-form jsForm', 'action' => '/'],
                'div' => ['style' => 'display:none;'],
                    'input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'],
                '/div',
        ];

        $helper = $this->_helper();
        $this->assertHtml($expected, $helper->create(false, ['jsForm' => true]));
        self::assertSame(
            '<input type="hidden" name="action" class="jsFormAction" value=""/></form>',
            $helper->end()
        );
    }

    public function testCreateProcessForm()
    {
        $Request = new ServerRequest([
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
        self::assertSame('<input type="hidden" name="action" class="jsFormAction" value=""/></form>', $Form->end());
    }
    
    public function testButton()
    {
        $this->assertHtml([
            'button' => ['data-title' => 'test', 'class' => 'ck-button', 'type' => 'submit'],
                'Test',
            '/button',
        ], $this->_helper()->button('Test', ['data-title' => 'test']));

        $this->assertHtml([
            'button' => ['class' => 'ck-button btn btn-default', 'type' => 'submit'],
                'Test',
            '/button',
        ], $this->_helper()->button('Test', ['button' => 'default']));

        $this->assertHtml([
            'button' => ['class' => 'ck-button', 'type' => 'submit'],
                'i' => ['class' => 'ck-icon fa fa-home'], '/i',
                'Test',
            '/button',
        ], $this->_helper()->button('Test', ['icon' => 'home']));
    }

    public function testInputValueByEntityContext()
    {
        $helper = $this->_helper();
        $entity = new Entity([
            'name'   => 'Test title',
            'email'  => 'test@google.com',
            'params' => [
                'name' => 'Test param'
            ]
        ]);

        TableRegistry::get('Forms', [
            'className' => __NAMESPACE__ . '\FormsTable'
        ]);

        $helper->create($entity, ['context' => ['table' => 'Forms']]);

        $this->assertHtml([
            'div' => ['class' => 'input email'],
                'label' => ['for' => 'email'],
                    'Email',
                '/label',
                'input' => [
                    'type'      => 'email',
                    'name'      => 'email',
                    'maxlength' => 255,
                    'id'        => 'email',
                    'value'     => 'test@google.com'
                ],
            '/div'
        ], $helper->control('email'));

        $this->assertHtml([
            'div' => ['class' => 'input text'],
                'label' => ['for' => 'name'],
                    'Name',
                '/label',
                'input' => [
                    'type'      => 'text',
                    'name'      => 'name',
                    'maxlength' => 255,
                    'id'        => 'name',
                    'value'     => 'Test title'
                ],
            '/div'
        ], $helper->control('name'));

        $this->assertHtml([
            'div' => ['class' => 'input text'],
                'label' => ['for' => 'test'],
                    'Test',
                '/label',
                'input' => ['type' => 'text', 'name' => 'test', 'id' => 'test'],
            '/div'
        ], $helper->control('test'));

        $this->assertHtml([
            'div' => ['class' => 'input text'],
                'label' => ['for' => 'params-name'],
                    'Name',
                '/label',
                'input' => [
                    'type'      => 'text',
                    'name'      => 'params[name]',
                    'maxlength' => 255,
                    'id'        => 'params-name',
                    'value'     => 'Test param'
                ],
            '/div'
        ], $helper->control('params.name'));
    }

    public function testCheckAll()
    {
        $this->assertHtml([
            'div' => ['class' => 'input checkbox'],
                'input' => ['type' => 'hidden', 'name' => 'check-all', 'value' => 0],
                'label' => ['for' => 'check-all'],
                    ['input' => [
                        'type'  => 'checkbox',
                        'name'  => 'check-all',
                        'value' => 1,
                        'class' => 'jsCheckAll',
                        'id'    => 'check-all'
                    ]],
                    'Check All',
                '/label',
            '/div'
        ], $this->_helper()->checkAll());
    }

    public function testProcessCheck()
    {
        $this->assertHtml([
            'div' => ['class' => 'input checkbox'],
                'input' => ['type' => 'hidden', 'name' => 'test[9][id]', 'value' => 0],
                'label' => ['for' => 'test-9-id'],
                    ['input' => [
                        'type'  => 'checkbox',
                        'name'  => 'test[9][id]',
                        'value' => 1,
                        'id'    => 'test-9-id'
                    ]],
                    'Id',
                '/label',
            '/div'
        ], $this->_helper()->processCheck('test', 9));
    }
}

/**
 * Class FormsTable
 *
 * @package Core\Test\TestCase\View\Helper
 */
class FormsTable extends Table
{

    /**
     * Default schema
     *
     * @var array
     */
    protected static $_tableSchema = [
        'id'        => ['type' => 'integer', 'null' => '', 'default' => '', 'length' => '8'],
        'name'      => ['type' => 'string', 'null' => '', 'default' => '', 'length' => '255'],
        'email'     => ['type' => 'string', 'null' => '', 'default' => '', 'length' => '255'],
        'phone'     => ['type' => 'string', 'null' => '', 'default' => '', 'length' => '255'],
        'password'  => ['type' => 'string', 'null' => '', 'default' => '', 'length' => '255'],
        'params'    => ['type' => 'text', 'null' => '', 'default' => ''],
        'published' => ['type' => 'date', 'null' => true, 'default' => null, 'length' => null],
        'created'   => ['type' => 'date', 'null' => '1', 'default' => '', 'length' => ''],
        'updated'   => ['type' => 'datetime', 'null' => '1', 'default' => '', 'length' => null],
        'age'       => ['type' => 'integer', 'null' => '', 'default' => '', 'length' => null],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

    /**
     * Initializes the schema.
     *
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->setSchema(self::$_tableSchema);
    }
}
