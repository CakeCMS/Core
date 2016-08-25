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

use Cake\View\View;
use Cake\Utility\Hash;
use Cake\Core\Configure;
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

        if ($isProcess != false) {
            $_options = [
                'url' => [
                    'plugin'     => $this->request->param('plugin'),
                    'controller' => $this->request->param('controller'),
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

        unset($options['process']);
        unset($options['jsForm']);

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
}
