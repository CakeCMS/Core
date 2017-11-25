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

use JBZoo\Utils\Arr;
use Cake\Event\Event;
use Cake\Utility\Hash;
use Cake\Routing\Router;

/**
 * Class JsHelper
 *
 * @package Core\View\Helper
 * @property \Core\View\Helper\HtmlHelper $Html
 * @property \Core\View\Helper\DocumentHelper $Document
 */
class JsHelper extends AppHelper
{

    /**
     * Uses helpers.
     *
     * @var array
     */
    public $helpers = [
        'Core.Html',
        'Core.Document',
    ];

    /**
     * Hold buffer scripts.
     *
     * @var array
     */
    protected $_buffers = [];

    /**
     * Before render callback.
     *
     * @param Event $event
     * @param $viewFile
     * @return void
     * @SuppressWarnings("unused")
     */
    public function beforeRender(Event $event, $viewFile)
    {
        $this->_setScriptVars();
    }

    /**
     * Get all holds in buffer scripts.
     *
     * @param array $options
     * @return null|string
     */
    public function getBuffer(array $options = [])
    {
        $docEol  = $this->Document->eol;
        $options = Hash::merge(['safe' => true], $options);

        if (Arr::key('block', $options)) {
            unset($options['block']);
        }

        if (count($this->_buffers)) {
            $scripts = $docEol .
                'jQuery (function($) {'  . $docEol .
                implode($docEol, $this->_buffers) . $docEol .
                '});' . $docEol;

            return $this->Html->scriptBlock($scripts, $options) . $docEol;
        }

        return null;
    }

    /**
     * Setup buffer script.
     *
     * @param string $script
     * @param bool|false $top
     * @return $this
     */
    public function setBuffer($script, $top = false)
    {
        $script = trim($script);
        if ($top) {
            array_unshift($this->_buffers, $script);
        } else {
            array_push($this->_buffers, $script);
        }

        return $this;
    }

    /**
     * Initialize java script widget.
     *
     * @param string $jSelector
     * @param string $widgetName
     * @param array $params
     * @param bool $return
     * @return string|null
     */
    public function widget($jSelector, $widgetName, array $params = [], $return = false)
    {
        static $included = [];

        $jSelector = is_array($jSelector) ? implode(', ', $jSelector) : $jSelector;

        $hash = $jSelector . ' /// ' . $widgetName;

        if (!Arr::key($hash, $included)) {
            $included[$hash] = true;
            $widgetName = str_replace('.', '', $widgetName);
            $initScript = '$("' . $jSelector . '").' . $widgetName . '(' . json_encode($params) . ');';

            if ($return) {
                return $this->Html->scriptBlock("\tjQuery(function($){" . $initScript . "});");
            }

            $this->setBuffer($initScript);
        }

        return null;
    }

    /**
     * Setup java script variables from server.
     *
     * @return void
     */
    protected function _setScriptVars()
    {
        $request = $this->request;
        $vars = [
            'baseUrl' => Router::fullBaseUrl(),
            'alert' => [
                'ok'     => __d('alert', 'Ok'),
                'cancel' => __d('alert', 'Cancel'),
                'sure'   => __d('alert', 'Are you sure?'),
            ],
            'request' => [
                'url'    => $request->url,
                'params' => [
                    'pass'       => $request->getParam('pass'),
                    'theme'      => $request->getParam('theme'),
                    'action'     => $request->getParam('action'),
                    'prefix'     => $request->getParam('prefix'),
                    'plugin'     => $request->getParam('plugin'),
                    'controller' => $request->getParam('controller'),
                ],
                'query'  => $this->request->getQueryParams(),
                'base'   => $request->getAttribute('base'),
                'here'   => $request->getRequestTarget(),
            ]
        ];

        $this->Html->scriptBlock('window.CMS = ' . json_encode($vars), ['block' => 'css_bottom']);
    }
}
