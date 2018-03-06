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

        $helper = new FormHelper($this->View, ['materializeCss' => true]);

        $expected = $helper->button('Tooltip', ['tooltip' => true, 'title' => 'Custom title']);
        $this->assertHtml([
            'button' => [
                'data-position' => 'top',
                'type'          => 'submit',
                'data-tooltip'  => 'Custom title',
                'title'         => 'Custom title',
                'class'         => 'ck-button hasTooltip',
            ],
                'Tooltip',
            '/button',
        ], $expected);

        $expected = $helper->button('Tooltip', ['tooltip' => 'Custom title']);
        $this->assertHtml([
            'button' => [
                'data-position' => 'top',
                'type'          => 'submit',
                'data-tooltip'  => 'Custom title',
                'title'         => 'Custom title',
                'class'         => 'ck-button hasTooltip',
            ],
                'Tooltip',
            '/button',
        ], $expected);

        $expected = $helper->button('Tooltip', ['tooltip' => 'Custom title', 'tooltipPos' => 'bottom']);
        $this->assertHtml([
            'button' => [
                'data-position' => 'bottom',
                'type'          => 'submit',
                'data-tooltip'  => 'Custom title',
                'title'         => 'Custom title',
                'class'         => 'ck-button hasTooltip',
            ],
                'Tooltip',
            '/button',
        ], $expected);
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

    public function testBeforeAfterInputContainer()
    {
        $helper = new FormHelper($this->View, ['materializeCss' => true]);

        $expected = $helper->control('before', ['before' => 'Before text']);
        $this->assertHtml([
            'div' => ['class' => 'input-field text'],
                'Before text',
                'input' => [
                    'type' => 'text',
                    'name' => 'before',
                    'id'   => 'before'
                ],
                'label' => ['for' => 'before'],
                    'Before',
                '/label',
            '/div'
        ], $expected);

        $expected = $helper->control('before', ['before' => 'icon:home']);
        $this->assertHtml([
            'div' => ['class' => 'input-field text'],
                'i' => ['class' => 'prefix ck-icon fa fa-home'], '/i',
                'input' => [
                    'type' => 'text',
                    'name' => 'before',
                    'id'   => 'before'
                ],
                'label' => ['for' => 'before'],
                    'Before',
                '/label',
            '/div'
        ], $expected);

        $expected = $helper->control('after', ['after' => 'icon:home']);
        $this->assertHtml([
            'div' => ['class' => 'input-field text'],
                'input' => [
                    'type' => 'text',
                    'name' => 'after',
                    'id'   => 'after'
                ],
                'label' => ['for' => 'after'],
                    'After',
                '/label',
                 'i' => ['class' => 'postfix ck-icon fa fa-home'], '/i',
            '/div'
        ], $expected);

        $expected = $helper->control('before_after', [
            'after'  => 'icon:home',
            'before' => 'icon:profile'
        ]);

        $this->assertHtml([
            'div' => ['class' => 'input-field text'],
                ['i' => ['class' => 'prefix ck-icon fa fa-profile']], '/i',
                'input' => [
                    'type' => 'text',
                    'name' => 'before_after',
                    'id'   => 'before-after'
                ],
                'label' => ['for' => 'before-after'],
                    'Before After',
                '/label',
                 ['i' => ['class' => 'postfix ck-icon fa fa-home']], '/i',
            '/div'
        ], $expected);
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

    public function testFile()
    {
        $this->assertHtml(['input' => ['type' => 'file', 'name' => 'file']], $this->_helper()->file('file'));
    }

    public function testSwitcher()
    {
        $this->assertHtml([
            ['input' => ['type' => 'hidden', 'name' => 'status', 'value' => 0]],
            ['input' => ['type' => 'checkbox', 'name' => 'status', 'value' => 1]]
        ], $this->_helper()->switcher('status'));
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

    public function testCheckMaterializeCssEnable()
    {
        $helper = new FormHelper($this->View, ['materializeCss' => true]);

        self::assertInstanceOf('Core\View\Helper\FormHelper', $helper);
        self::assertTrue($helper->getConfig('materializeCss'));
    }

    public function testCheckMaterializeSwitcher()
    {
        $helper = new FormHelper($this->View, ['materializeCss' => true]);

        $expected =
            '<div class="switch">' .
                '<div class="switch-title">custom</div>' .
                '<label>' .
                'Off' .
                    '<input type="hidden" name="custom" value="0"/>' .
                    '<input type="checkbox" name="custom" value="1" class="filled-in">' .
                    '<span class="lever"></span>' .
                'On' .
                '</label>' .
            '</div>';

        $this->assertSame($expected, $helper->switcher('custom'));
    }

    public function testCheckMaterializeInput()
    {
        $helper = new FormHelper($this->View, ['materializeCss' => true]);

        $expected = [
            'div' => ['class' => 'input-field text'],
                'input' => ['type' => 'text', 'name' => 'test', 'id' => 'test'],
                'label' => ['for' => 'test'],
                    'Test',
                '/label',
            '/div'
        ];

        $this->assertHtml($expected, $helper->control('test'));

        $expected = [
            'div' => ['class' => 'input-field textarea'],
                'textarea' => [
                    'rows'  => 5,
                    'name'  => 'test',
                    'id'    => 'test',
                    'class' => 'materialize-textarea',
                ],
                '/textarea',
                'label' => ['for' => 'test'],
                    'Test',
                '/label',
            '/div'
        ];
        $this->assertHtml($expected, $helper->control('test', ['type' => 'textarea']));

        $expected = [
            'div' => ['class' => 'input-field textarea'],
                'textarea' => [
                    'rows'  => 5,
                    'name'  => 'test',
                    'id'    => 'test',
                    'class' => 'custom-class materialize-textarea'
                ],
                '/textarea',
                'label' => ['for' => 'test'],
                    'Test',
                '/label',
            '/div'
        ];
        $this->assertHtml($expected, $helper->control('test', [
            'type'  => 'textarea',
            'class' => 'custom-class'
        ]));
    }

    public function testCheckMaterializeFile()
    {
        $helper = new FormHelper($this->View, ['materializeCss' => true]);

        $expected = [
            'div' => ['class' => 'input-field file'],
                ['div' => ['class' => 'file-field input-field']],
                    ['div' => ['class' => 'btn']],
                        ['span' => []],
                            'image',
                        '/span',
                        ['input' => ['type' => 'file', 'name' => 'image']],
                    '/div',
                    ['div' => ['class' => 'file-path-wrapper']],
                        ['input' => ['class' => 'file-path', 'type' => 'text']],
                    '/div',
                '/div',
            '/div'
        ];

        $this->assertHtml($expected, $helper->file('image'));

        $expected = [
            'div' => ['class' => 'input-field file'],
                ['div' => ['class' => 'file-field input-field']],
                    ['div' => ['class' => 'btn']],
                        ['span' => []],
                            'Custom title',
                        '/span',
                        ['input' => ['type' => 'file', 'name' => 'avatar']],
                    '/div',
                    ['div' => ['class' => 'file-path-wrapper']],
                        ['input' => ['class' => 'file-path', 'type' => 'text']],
                    '/div',
                '/div',
            '/div'
        ];

        $this->assertHtml($expected, $helper->file('avatar', [
            'title' => 'Custom title'
        ]));
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
