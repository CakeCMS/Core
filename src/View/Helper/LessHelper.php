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

use Core\Plugin;
use JBZoo\Utils\FS;
use JBZoo\Utils\Str;
use Core\Lib\Less\Less;
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Utility\Inflector;

/**
 * Class LessHelper
 *
 * @package Core\View\Helper
 * @property \Core\View\Helper\UrlHelper $Url
 */
class LessHelper extends AppHelper
{

    /**
     * List of helpers used by this helper.
     *
     * @var array
     */
    public $helpers = [
        'Url' => ['className' => 'Core.Url'],
    ];

    /**
     * Constructor hook method.
     *
     * @param array $config
     */
    public function initialize(array $config)
    {
        $corePath  = Plugin::path('Core') . Configure::read('App.webroot') . '/' . Configure::read('App.lessBaseUrl');
        $cachePath = WWW_ROOT . Configure::read('App.cssBaseUrl') . Configure::read('App.cacheDir');

        $this->config('root_path', APP_ROOT);
        $this->config('cache_path', $cachePath);
        $this->config('root_url', Router::fullBaseUrl());
        $this->config('debug', Configure::read('debug'));
        $this->config('import_paths', [realpath($corePath) => realpath($corePath)]);

        parent::initialize($config);
    }

    /**
     * Process less file.
     *
     * @param string $source
     * @param bool $force
     * @return string|null
     * @throws \JBZoo\Less\Exception
     */
    public function process($source, $force = true)
    {
        list ($webRoot, $source) = $this->_findWebRoot($source);
        $lessFile = FS::clean($webRoot . Configure::read('App.lessBaseUrl') . $source, '/');

        $this->_setForce($force);
        $less = new Less($this->_config);

        if (!FS::isFile($lessFile)) {
            return null;
        }

        list($source, $isExpired) = $less->compile($lessFile);

        if ($isExpired) {
            $cacheId  = FS::firstLine($source);
            $comment  = '/* resource:' . str_replace(FS::clean(ROOT, '/'), '', $lessFile) . ' */' . PHP_EOL;
            $fileHead = implode('', [$cacheId, Str::low($comment)]);

            $css = $this->_normalizeContent($source, $fileHead);
            $this->_write($source, $css);
        }

        $source = str_replace(FS::clean(APP_ROOT . '/' . Configure::read('App.webroot'), '/'), '', $source);
        $source = str_replace(Configure::read('App.cssBaseUrl'), '', $source);

        return $source;
    }

    /**
     * CSS compressing.
     *
     * @param string $code
     * @param string $cacheId
     * @return string
     */
    protected function _compress($code, $cacheId)
    {
        $code = (string) $code;

        // remove comments
        $code = preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*/#ius', '', $code);

        $code = str_replace(
            ["\r\n", "\r", "\n", "\t", '  ', '    ', ' {', '{ ', ' }', '; ', ';;', ';;;', ';;;;', ';}'],
            ['', '', '', '', '', '', '{', '{', '}', ';', ';', ';', ';', '}'],
            $code
        ); // remove tabs, spaces, newlines, etc.

        // remove spaces after and before colons
        $code = preg_replace('#([a-z\-])(:\s*|\s*:\s*|\s*:)#ius', '$1:', $code);

        // spaces before "!important"
        $code = preg_replace('#(\s*\!important)#ius', '!important', $code);
        $code = Str::trim($code);

        return implode('', [$cacheId, $code]);
    }

    /**
     * Find source webroot dir.
     *
     * @param string $source
     * @return array
     */
    protected function _findWebRoot($source)
    {
        $webRootDir = Configure::read('App.webroot');
        $webRoot    = APP_ROOT . DS . $webRootDir . DS;

        list ($plugin, $source) = $this->_View->pluginSplit($source);
        if ($plugin !== null && Plugin::loaded($plugin)) {
            $webRoot = Plugin::path($plugin) . $webRootDir . DS;
        }

        return [FS::clean($webRoot, '/'), $source];
    }

    /**
     * Get asset url by source.
     *
     * @param string $source (Example: TestPlugin.path/to/image.png)
     * @return string
     */
    protected function _getAssetUrl($source)
    {
        return 'url("' . $this->Url->assetUrl($source, ['fullBase' => true]) . '")';
    }

    /**
     * Get plugin asset url.
     *
     * @param string $path
     * @return array
     */
    protected function _getPlgAssetUrl($path)
    {
        $isPlugin = false;
        $plgPaths = Configure::read('App.paths.plugins');
        foreach ($plgPaths as $plgPath) {
            $plgPath = ltrim(FS::clean($plgPath, '/'), '/');
            if (preg_match('(' . quotemeta($plgPath) . ')', $path)) {
                $path = str_replace($plgPath, '', $path);
                return [true, $path];
            }
        }

        foreach (Configure::read('plugins') as $name => $plgPath) {
            $plgPath = FS::clean($plgPath, '/');
            if (preg_match('(' . quotemeta($plgPath) . ')', $path)) {
                $path = Str::low($name) . '/' . str_replace($plgPath, '', $path);
                return [true, $path];
            }
        }

        return [$isPlugin, $path];
    }

    /**
     * Normalize style file.
     *
     * @param string $path
     * @param string $fileHead
     * @return mixed
     */
    protected function _normalizeContent($path, $fileHead)
    {
        $css = file_get_contents($path);
        if (!$this->_configRead('debug')) {
            $css = $this->_compress($css, $fileHead);
        } else {
            list ($first, $second) = explode(PHP_EOL, $css, 2);
            if (preg_match('(\/* cacheid:)', $first)) {
                $css = $second;
            }

            $css = implode('', [$fileHead, $css]);
        }

        return $this->_replaceUrl($css);
    }

    /**
     * Normalize plugin asset url.
     *
     * @param string $path
     * @return string
     */
    protected function _normalizePlgAssetUrl($path)
    {
        $details = explode('/', $path, 3);
        $pluginName = Inflector::camelize(trim($details[0], '/'));

        if (Plugin::loaded($pluginName)) {
            unset($details[0]);
            $source = $pluginName . '.' . ltrim(implode('/', $details), '/');
            return $this->_getAssetUrl($source);
        }

        return $this->_getAssetUrl($path);
    }

    /**
     * Preg replace url.
     *
     * @param string $css
     * @return mixed
     */
    protected function _replaceUrl($css)
    {
        $pattern = '/url\\(\\s*([\'"](.*?)[\'"]|[^\\)\\s]+)\\s*\\)/';
        return preg_replace_callback($pattern, [__CLASS__, '_replaceUrlCallback'], $css);
    }

    /**
     * Replace url callback.
     *
     * @param array $match
     * @return string
     */
    protected function _replaceUrlCallback(array $match)
    {
        $assetPath = str_replace(Router::fullBaseUrl(), '', $match[2]);
        $assetPath = trim(FS::clean($assetPath, '/'), '/');
        $appDir    = trim(FS::clean(APP_ROOT, '/'), '/');

        list ($isPlugin, $assetPath) = $this->_getPlgAssetUrl($assetPath);

        if (!$isPlugin) {
            $assetPath = str_replace($appDir, '', $assetPath);
        }

        $assetPath = str_replace(Configure::read('App.webroot'), '', $assetPath);
        $assetPath = FS::clean($assetPath, '/');

        if ($isPlugin) {
            return $this->_normalizePlgAssetUrl($assetPath);
        }

        return $this->_getAssetUrl($assetPath);
    }

    /**
     * Set less process force.
     *
     * @param bool $force
     * @return void
     */
    protected function _setForce($force = false)
    {
        if ($force) {
            $this->config('force', true);
        }
    }

    /**
     * Write content in to the file.
     *
     * @param string $path
     * @param string $content
     * @return void
     */
    protected function _write($path, $content)
    {
        $File = new File($path);
        $File->write($content);
        $File->exists();
    }
}
