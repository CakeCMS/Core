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

use Core\Core\Plugin;
use JBZoo\Utils\Arr;
use JBZoo\Utils\Str;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * Class DocumentHelper
 *
 * @package     Core\View\Helper
 * @property    \Core\View\Helper\HtmlHelper $Html
 * @property    \Core\View\Helper\AssetsHelper $Assets
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DocumentHelper extends AppHelper
{

    /**
     * Init vars.
     *
     * @var string
     */
    public $charset;
    public $dir;
    public $eol;
    public $locale;
    public $tab;

    /**
     * Uses helpers.
     *
     * @var array
     */
    public $helpers = [
        'Core.Html',
        'Core.Assets'
    ];

    /**
     * Is called after layout rendering is complete. Receives the layout filename as an argument.
     *
     * @param   Event $event
     * @param   string $layoutFile
     * @return  void
     *
     * @throws  \JBZoo\Utils\Exception
     */
    public function afterLayout(Event $event, $layoutFile)
    {
        $pluginEvent = Plugin::getData('Core', 'View.afterLayout');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('View.afterLayout')) {
            call_user_func_array($pluginEvent->find(0), [$this->getView(), $event, $layoutFile]);
        }
    }

    /**
     * Is called after the view has been rendered but before layout rendering has started.
     *
     * @param   Event $event
     * @param   string $viewFile
     * @return  void
     *
     * @throws  \JBZoo\Utils\Exception
     */
    public function afterRender(Event $event, $viewFile)
    {
        $this->_setupMetaData();

        $pluginEvent = Plugin::getData('Core', 'View.afterRender');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('View.afterRender')) {
            call_user_func_array($pluginEvent->find(0), [$this->_View, $event, $viewFile]);
        }
    }

    /**
     * Is called after each view file is rendered. This includes elements, views, parent views and layouts.
     * A callback can modify and return $content to change how the rendered content will be displayed in the browser.
     *
     * @param   Event $event
     * @param   string $viewFile
     * @param   string $content
     * @return  void
     *
     * @throws  \JBZoo\Utils\Exception
     */
    public function afterRenderFile(Event $event, $viewFile, $content)
    {
        $pluginEvent = Plugin::getData('Core', 'View.afterRenderFile');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('View.afterRenderFile')) {
            call_user_func_array($pluginEvent->find(0), [$this->getView(), $event, $viewFile, $content]);
        }
    }

    /**
     * Get assets fot layout render.
     *
     * @param   string $type
     * @return  string
     */
    public function assets($type = 'css')
    {
        $output = [];
        $assets = $this->Assets->getAssets($type);
        foreach ($assets as $asset) {
            $output[] = $asset['output'];
        }

        return implode($this->eol, $output);
    }

    /**
     * Is called before layout rendering starts. Receives the layout filename as an argument.
     *
     * @param   Event $event
     * @param   string $layoutFile
     * @return  void
     *
     * @throws  \JBZoo\Utils\Exception
     */
    public function beforeLayout(Event $event, $layoutFile)
    {
        $pluginEvent = Plugin::getData('Core', 'View.beforeLayout');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('View.beforeLayout')) {
            call_user_func_array($pluginEvent->find(0), [$this->getView(), $event, $layoutFile]);
        }
    }

    /**
     * Is called after the controller’s beforeRender method but before the controller renders view and layout.
     * Receives the file being rendered as an argument.
     *
     * @param   Event $event
     * @param   string $viewFile
     * @return  void
     *
     * @throws  \JBZoo\Less\Exception
     * @throws  \JBZoo\Utils\Exception
     */
    public function beforeRender(Event $event, $viewFile)
    {
        $this->Assets->loadPluginAssets();

        $pluginEvent = Plugin::getData('Core', 'View.beforeRender');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('View.beforeRender')) {
            call_user_func_array($pluginEvent->find(0), [$this->getView(), $event, $viewFile]);
        }
    }

    /**
     * Is called before each view file is rendered. This includes elements, views, parent views and layouts.
     *
     * @param    Event $event
     * @param   string $viewFile
     * @return  void
     *
     * @throws  \JBZoo\Utils\Exception
     */
    public function beforeRenderFile(Event $event, $viewFile)
    {
        $pluginEvent = Plugin::getData('Core', 'View.beforeRenderFile');
        if (is_callable($pluginEvent->find(0)) && Plugin::hasManifestEvent('View.beforeRenderFile')) {
            call_user_func_array($pluginEvent->find(0), [$this->getView(), $event, $viewFile]);
        }
    }

    /**
     * Get body classes by view data.
     *
     * @return  string
     */
    public function getBodyClasses()
    {
        $view    = $this->getView();
        $request = $view->getRequest();
        $prefix  = ($request->getParam('prefix')) ? 'prefix-' . $request->getParam('prefix') : 'prefix-site';

        $classes = [
            $prefix,
            'theme-'    . Str::low($view->getTheme()),
            'plugin-'   . Str::low($view->getPlugin()),
            'view-'     . Str::low($view->getName()),
            'tmpl-'     . Str::low($view->getTemplate()),
            'layout-'   . Str::low($view->getLayout())
        ];

        $pass = (array) $request->getParam('pass');
        if (count($pass)) {
            $classes[] = 'item-id-' . array_shift($pass);
        }

        return implode(' ', $classes);
    }

    /**
     * Create head for layout.
     *
     * @return  string
     */
    public function head()
    {
        $output = [
            'meta'             => $this->getView()->fetch('meta'),
            'assets'           => $this->assets('css'),
            'fetch_css'        => $this->getView()->fetch('css'),
            'fetch_css_bottom' => $this->getView()->fetch('css_bottom'),
        ];

        return implode('', $output);
    }

    /**
     * Constructor hook method.
     *
     * @param   array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->dir     = Configure::read('Cms.docDir');
        $this->locale  = Configure::read('App.defaultLocale');
        $this->charset = Str::low(Configure::read('App.encoding'));
        $this->eol     = (Configure::read('debug')) ? PHP_EOL : '';
        $this->tab     = (Configure::read('debug')) ? Configure::read('Cms.lineTab') : '';
    }

    /**
     * Site language.
     *
     * @param   bool|true $isLang
     * @return  string
     *
     * @throws  \Exception
     */
    public function lang($isLang = true)
    {
        list($lang, $region) = explode('_', $this->locale);
        return ($isLang) ? Str::low($lang) : Str::low($region);
    }

    /**
     * Creates a link to an external resource and handles basic meta tags.
     *
     * @param   array $rows
     * @param   null $block
     * @return  null|string
     */
    public function meta(array $rows, $block = null)
    {
        $output = [];
        foreach ($rows as $row) {
            $output[] = trim($row);
        }

        $output = implode($this->eol, $output) . $this->eol;

        if ($block !== null) {
            $this->getView()->append($block, $output);
            return null;
        }

        return $output;
    }

    /**
     * Create html 5 document type.
     *
     * @return  string
     *
     * @throws  \Exception
     */
    public function type()
    {
        $lang = $this->lang();
        $html = [
            '<!doctype html>',
            '<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7 ie6" '
            . 'lang="' . $lang . '" dir="' . $this->dir . '"> <![endif]-->',
            '<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" '
            . 'lang="' . $lang . '" dir="' . $this->dir . '"> <![endif]-->',
            '<!--[if IE 8]><html class="no-js lt-ie9 ie8" '
            . 'lang="' . $lang . '" dir="' . $this->dir . '"> <![endif]-->',
            '<!--[if gt IE 8]><!--><html class="no-js" xmlns="http://www.w3.org/1999/xhtml" '
            . 'lang="' . $lang . '" dir="' . $this->dir . '" '
            . 'prefix="og: http://ogp.me/ns#" '
            . '> <!--<![endif]-->',
        ];
        return implode($this->eol, $html) . $this->eol;
    }

    /**
     * Assign data from view vars.
     *
     * @param   string $key
     * @return  $this
     */
    protected function _assignMeta($key)
    {
        if (Arr::key($key, $this->_View->viewVars)) {
            $this->getView()->assign($key, $this->_View->viewVars[$key]);
        }

        return $this;
    }

    /**
     * Setup view meta data.
     *
     * @return  void
     */
    protected function _setupMetaData()
    {
        $this
            ->_assignMeta('page_title')
            ->_assignMeta('meta_keywords')
            ->_assignMeta('meta_description');
    }
}
