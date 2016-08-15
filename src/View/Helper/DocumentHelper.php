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

use JBZoo\Utils\Str;
use Cake\Core\Configure;

/**
 * Class DocumentHelper
 *
 * @package Core\View\Helper
 * @property \Core\View\Helper\HtmlHelper $Html
 */
class DocumentHelper extends AppHelper
{

    /**
     * Init vars.
     *
     * @var string
     */
    public $dir;
    public $eol;
    public $tab;
    public $locale;
    public $charset;

    /**
     * Uses helpers.
     *
     * @var array
     */
    public $helpers = [
        'Core.Html',
    ];

    /**
     * Constructor hook method.
     *
     * @param array $config
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
     * @param bool|true $isLang
     * @return string
     * @throws \Exception
     */
    public function lang($isLang = true)
    {
        list($lang, $region) = explode('_', $this->locale);
        return ($isLang) ? Str::low($lang) : Str::low($region);
    }

    /**
     * Creates a link to an external resource and handles basic meta tags.
     *
     * @param array $rows
     * @param null $block
     * @return null|string
     */
    public function meta(array $rows, $block = null)
    {
        $output = [];
        foreach ($rows as $row) {
            $output[] = trim($row);
        }

        $output = implode($this->eol, $output) . $this->eol;

        if ($block !== null) {
            $this->_View->append($block, $output);
            return null;
        }

        return $output;
    }

    /**
     * Create html 5 document type.
     *
     * @return string
     * @throws \Exception
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
}
