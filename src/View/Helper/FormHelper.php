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

namespace Core\View\Helper;

use Cake\Form\Form;
use Cake\View\View;
use Cake\Utility\Hash;
use Cake\Core\Configure;
use Core\View\Form\FormContext;
use Cake\Collection\Collection;
use Core\View\Form\ArrayContext;
use Core\View\Form\EntityContext;
use Cake\Datasource\EntityInterface;
use Core\View\Helper\Traits\HelperTrait;
use Cake\View\Helper\FormHelper as CakeFormHelper;

/**
 * Class FormHelper
 *
 * @package Core\View\Helper
 * @property \Core\View\Helper\UrlHelper $Url
 * @property \Core\View\Helper\HtmlHelper $Html
 */
class FormHelper extends CakeFormHelper
{

    use HelperTrait;

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
     * @param View $View
     * @param array $config
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
        $this->_configWrite('btnPref', Configure::read('Cms.btnPref'));
        $this->_configWrite('iconPref', Configure::read('Cms.iconPref'));
        $this->_configWrite('classPrefix', Configure::read('Cms.classPrefix'));
    }

    /**
     * Creates a `<button>` tag.
     *
     * @param string $title
     * @param array $options
     * @return string
     */
    public function button($title, array $options = [])
    {
        $options = $this->addClass($options, $this->_class(__FUNCTION__));
        $options = $this->_getBtnClass($options);

        list($title, $options) = $this->_createIcon($this->Html, $title, $options);

        return parent::button($title, $options);
    }

    /**
     * Input check all.
     *
     * @return string
     */
    public function checkAll()
    {
        return $this->control('check-all', ['type' => 'checkbox', 'class' => 'jsCheckAll']);
    }

    /**
     * Create html form.
     *
     * @param mixed $model
     * @param array $options
     * @return string
     */
    public function create($model = null, array $options = [])
    {
        $options += ['process' => false, 'jsForm' => false];
        $options = $this->addClass($options, $this->_class('form'));

        $isProcess = $options['process'];

        if ($isProcess !== false) {
            $_options = [
                'url' => [
                    'plugin'     => $this->request->getParam('plugin'),
                    'controller' => $this->request->getParam('controller'),
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
     * @param array $secureAttributes
     * @return string
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
     * @param string $name
     * @param string $type
     * @return string
     */
    public function processCheck($type, $name)
    {
        return $this->control($type . '.' . $name . '.id', ['type' => 'checkbox']);
    }

    /**
     * Add the default suite of context providers provided.
     *
     * @return void
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
     * Add the entity suite of context providers provided.
     *
     * @param $request
     * @param $data
     * @return EntityContext
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
     * @return void
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
     * @return void
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
