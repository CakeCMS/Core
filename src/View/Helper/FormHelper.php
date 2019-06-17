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

namespace Core\View\Helper;

use Cake\Form\Form;
use Cake\View\View;
use JBZoo\Data\Data;
use JBZoo\Utils\Arr;
use JBZoo\Utils\Str;
use Cake\View\Helper;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Core\View\Form\FormContext;
use Cake\Collection\Collection;
use Core\View\Form\ArrayContext;
use Core\View\Form\EntityContext;
use Cake\Datasource\EntityInterface;
use Core\View\Helper\Traits\HelperTrait;
use Core\View\Helper\Traits\MaterializeCssTrait;
use Cake\View\Helper\FormHelper as CakeFormHelper;

/**
 * Class FormHelper
 *
 * @package     Core\View\Helper
 * @property    \Core\View\Helper\UrlHelper $Url
 * @property    \Core\View\Helper\HtmlHelper $Html
 */
class FormHelper extends CakeFormHelper
{

    use HelperTrait, MaterializeCssTrait;

    /**
     * List of helpers used by this helper.
     *
     * @var array
     */
    public $helpers = [
        'Url'  => ['className' => 'Core.Url'],
        'Html' => ['className' => 'Core.Html'],
    ];

    /**
     * Hold js form type.
     *
     * @var bool
     */
    protected $_isJsForm = false;

    /**
     * HtmlHelper constructor.
     *
     * @param   View $View
     * @param   array $config
     */
    public function __construct(View $View, array $config = [])
    {
        $this->_defaultConfig = Hash::merge([
            'materializeCss' => false,
            'btnPref'        => Configure::read('Cms.btnPref'),
            'iconPref'       => Configure::read('Cms.iconPref'),
            'classPrefix'    => Configure::read('Cms.classPrefix'),
        ], $this->_defaultConfig);

        $config = new Data($config);

        if ($config->get('materializeCss', false) === true) {
            $config
                ->set('widgets', [
                    'file'     => 'Core\View\Widget\MaterializeCss\FileWidget',
                    'textarea' => 'Core\View\Widget\MaterializeCss\TextareaWidget',
                    'checkbox' => 'Core\View\Widget\MaterializeCss\CheckboxWidget'
                ])
                ->set('templates', 'Core.templates/materialize_css_form')
                ->set('prepareBtnClass', function (Helper $form, $options, $button) {
                    return $this->_prepareBtn($form, $options, $button);
                })
                ->set('prepareTooltip', function (Helper $html, $options, $tooltip) {
                    return $this->_prepareTooltip($html, $options, $tooltip);
                });
        }

        $widgets = Hash::merge(['_default' => 'Core\View\Widget\BasicWidget'], $config->get('widgets', []));

        $config->set('widgets', $widgets);

        parent::__construct($View, $config->getArrayCopy());
    }

    /**
     * Creates file input widget.
     *
     * @param   string $fieldName Name of a field, in the form "modelname.fieldname"
     * @param   array $options Array of HTML attributes.
     *
     * @return  string A generated file input.
     */
    public function file($fieldName, array $options = [])
    {
        $errorSuffix = null;
        $error       = $this->error($fieldName);
        $content     = parent::file($fieldName, $options);

        if ($this->getConfig('materializeCss', false) === false) {
            return $content;
        }

        $options = $this->_parseOptions($fieldName, $options);

        $options['type'] = __FUNCTION__;
        if ($error !== '') {
            $options['error'] = $error;
            $errorSuffix = 'Error';
        }

        $result = $this->_inputContainerTemplate([
            'error'       => $error,
            'content'     => $content,
            'options'     => $options,
            'errorSuffix' => $errorSuffix
        ]);

        return $result;
    }

    /**
     * Form switcher.
     *
     * @param   string $fieldName
     * @param   array $options
     * @return  string
     */
    public function switcher($fieldName, array $options = [])
    {
        $input = parent::checkbox($fieldName, $options);

        if ($this->getConfig('materializeCss', false) === false) {
            return $input;
        }

        $options += [
            'before' => __d('backend', 'Off'),
            'after'  => __d('backend', 'On')
        ];

        $title = (Arr::key('title', $options)) ? $options['title'] : $fieldName;

        if (!empty($title)) {
            $title = $this->Html->div('switch-title', $title);
        }

        $content = $this->formatTemplate(__FUNCTION__, [
            'input'  => $input,
            'title'  => $title,
            'after'  => $options['after'],
            'before' => $options['before'],
            'lever'  => '<span class="lever"></span>'
        ]);

        return $content;
    }

    /**
     * Creates a `<button>` tag.
     *
     * @param   string $title
     * @param   array $options
     * @return  string
     */
    public function button($title, array $options = [])
    {
        $options = $this->addClass($options, $this->_class(__FUNCTION__));
        $options = $this->_getBtnClass($options);
        $options = $this->_getToolTipAttr($options);

        list($title, $options) = $this->_createIcon($this->Html, $title, $options);

        return parent::button($title, $options);
    }

    /**
     * Input check all.
     *
     * @return  string
     */
    public function checkAll()
    {
        return $this->control('check-all', ['type' => 'checkbox', 'class' => 'jsCheckAll']);
    }

    /**
     * Create html form.
     *
     * @param   mixed $model
     * @param   array $options
     * @return  string
     */
    public function create($model = null, array $options = [])
    {
        $options += ['process' => false, 'jsForm' => false];
        $options = $this->addClass($options, $this->_class('form'));
        $request = $this->getView()->getRequest();

        $isProcess = $options['process'];

        if ($isProcess !== false) {
            $_options = [
                'url' => [
                    'plugin'     => $request->getParam('plugin'),
                    'controller' => $request->getParam('controller'),
                    'action'     => 'process'
                ]
            ];

            $options['jsForm'] = true;
            $options = Hash::merge($_options, $options);
        }

        $isJsForm = $options['jsForm'];
        if ($isJsForm) {
            $this->_isJsForm = true;
            $options = $this->addClass($options, 'jsForm');
        }

        unset($options['process'], $options['jsForm']);

        return parent::create($model, $options);
    }

    /**
     * End html form.
     *
     * @param   array $secureAttributes
     * @return  string
     */
    public function end(array $secureAttributes = [])
    {
        if ($this->_isJsForm) {
            return implode('', [
                $this->hidden('action', ['value' => '', 'class' => 'jsFormAction']),
                parent::end($secureAttributes)
            ]);
        }

        return parent::end($secureAttributes);
    }

    /**
     * Table row process checkbox.
     *
     * @param   string $name
     * @param   string $type
     * @return  string
     */
    public function processCheck($type, $name)
    {
        return $this->control($type . '.' . $name . '.id', ['type' => 'checkbox']);
    }

    /**
     * Add the default suite of context providers provided.
     *
     * @return  void
     */
    protected function _addDefaultContextProviders()
    {
        $this->addContextProvider('orm', function ($request, $data) {
            if (is_array($data['entity']) || $data['entity'] instanceof \Traversable) {
                $pass = (new Collection($data['entity']))->first() !== null;
                if ($pass) {
                    return new EntityContext($request, $data);
                }
            }

            return $this->_addEntityContent($request, $data);
        });

        $this->_addFormContextProvider();
        $this->_addFormArrayProvider();
    }

    /**
     * Generates an input container template
     *
     * @param   array $options The options for input container template
     * @return  string The generated input container template
     */
    protected function _inputContainerTemplate($options)
    {
        $inputContainerTemplate = $options['options']['type'] . 'Container' . $options['errorSuffix'];
        if (!$this->templater()->get($inputContainerTemplate)) {
            $inputContainerTemplate = 'inputContainer' . $options['errorSuffix'];
        }

        $_options = new Data($options['options']);
        $before   = $this->_prepareBeforeAfterContainer('before', $_options->get('before'));
        $after    = $this->_prepareBeforeAfterContainer('after', $_options->get('after'));

        return $this->formatTemplate($inputContainerTemplate, [
            'after'         => $after,
            'before'        => $before,
            'error'         => $options['error'],
            'content'       => $options['content'],
            'type'          => $options['options']['type'],
            'required'      => $options['options']['required'] ? ' required' : '',
            'templateVars'  => isset($options['options']['templateVars']) ? $options['options']['templateVars'] : []
        ]);
    }

    /**
     * Add the entity suite of context providers provided.
     *
     * @param   $request
     * @param   $data
     * @return  EntityContext
     */
    protected function _addEntityContent($request, $data)
    {
        if ($data['entity'] instanceof EntityInterface) {
            return new EntityContext($request, $data);
        }

        if (is_array($data['entity']) && empty($data['entity']['schema'])) {
            return new EntityContext($request, $data);
        }
    }

    /**
     * Add the array suite of context providers provided.
     *
     * @return  void
     */
    protected function _addFormArrayProvider()
    {
        $this->addContextProvider('array', function ($request, $data) {
            if (is_array($data['entity']) && isset($data['entity']['schema'])) {
                return new ArrayContext($request, $data['entity']);
            }
        });
    }

    /**
     * Add the form suite of context providers provided.
     *
     * @return  void
     */
    protected function _addFormContextProvider()
    {
        $this->addContextProvider('form', function ($request, $data) {
            if ($data['entity'] instanceof Form) {
                return new FormContext($request, $data);
            }
        });
    }
}
