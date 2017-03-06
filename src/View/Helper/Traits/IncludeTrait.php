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

namespace Core\View\Helper\Traits;

use JBZoo\Utils\Str;

/**
 * Class IncludeTrait
 *
 * @package Core\View\Helper\Traits
 * @property \Core\View\Helper\DocumentHelper $Document
 * @property \Core\View\Helper\UrlHelper $Url
 * @property \Core\View\AppView _View
 * @property array _includedAssets
 * @property array _assets
 * @method \Cake\View\StringTemplate templater()
 * @method string formatTemplate($name, $data)
 */
trait IncludeTrait
{

    /**
     * Include array paths.
     *
     * @param array|string $path
     * @param array $options
     * @param string $type
     * @return bool|null|string
     */
    protected function _arrayInclude($path, array $options = [], $type = 'css')
    {
        $doc = $this->Document;
        if (is_array($path)) {
            $out = '';
            foreach ($path as $i) {
                $out .= $this->{$type}($i, $options);
            }

            if (empty($options['block'])) {
                return $out . $doc->eol;
            }

            return null;
        }

        return false;
    }

    /**
     * Get current asset type.
     *
     * @param string $type
     * @return string
     */
    protected function _getAssetType($type = 'css')
    {
        return ($type === 'script') ? 'js' : $type;
    }

    /**
     * Get current css output.
     *
     * @param array $options
     * @param string $url
     * @return string
     */
    protected function _getCssOutput(array $options, $url)
    {
        if ($options['rel'] === 'import') {
            return $this->formatTemplate('style', [
                'attrs'   => $this->templater()->formatAttributes($options, ['rel', 'block', 'weight', 'alias']),
                'content' => '@import url(' . $url . ');',
            ]);
        }

        return $this->formatTemplate('css', [
            'rel'   => $options['rel'],
            'url'   => $url,
            'attrs' => $this->templater()->formatAttributes($options, ['rel', 'block', 'weight', 'alias']),
        ]);
    }

    /**
     * Get current asset path.
     *
     * @param string|array $path
     * @param string|array $assetArray
     * @return string
     */
    protected function _getCurrentPath($path, $assetArray)
    {
        return (is_array($path)) ? (string) $assetArray : (string) $path;
    }

    /**
     * Get current options and url asset.
     *
     * @param string $path
     * @param array $options
     * @param string $type
     * @param bool $external
     * @return array
     */
    protected function _getCurrentUrlAndOptions($path, array $options, $type, $external)
    {
        if (strpos($path, '//') !== false) {
            $url      = $path;
            $external = true;
            unset($options['fullBase']);
        } else {
            $url = $this->Url->{$type}($path, $options);
            $options = array_diff_key($options, ['fullBase' => null, 'pathPrefix' => null]);
        }

        return [$url, $options, $external];
    }

    /**
     * Get current script output.
     *
     * @param array $options
     * @param string $url
     * @return string
     */
    protected function _getScriptOutput(array $options, $url)
    {
        return $this->formatTemplate('javascriptlink', [
            'url'   => $url,
            'attrs' => $this->templater()->formatAttributes($options, ['block', 'once', 'weight', 'alias']),
        ]);
    }

    /**
     * Get current output by type.
     *
     * @param array $options
     * @param string $url
     * @param string $type
     * @return string
     */
    protected function _getTypeOutput(array $options, $url, $type)
    {
        $type = Str::low($type);
        if ($type === 'css') {
            return $this->_getCssOutput($options, $url);
        }

        return $this->_getScriptOutput($options, $url);
    }

    /**
     * Check has asset.
     *
     * @param string $path
     * @param string $type
     * @param bool $external
     * @return bool
     */
    protected function _hasAsset($path, $type, $external)
    {
        return !$this->Url->assetPath($path, $this->_getAssetType($type)) && $external === false;
    }

    /**
     * Include asset.
     *
     * @param string|array $path
     * @param array $options
     * @param string $type
     * @return bool|null|string
     */
    protected function _include($path, array $options = [], $type = 'css')
    {
        $doc = $this->Document;
        $options += ['once' => true, 'block' => null, 'fullBase' => true, 'weight' => 10];
        $external = false;

        $assetArray = $this->_arrayInclude($path, $options, $type);
        if ($assetArray) {
            return $assetArray;
        }

        $path = $this->_getCurrentPath($path, $assetArray);
        if (empty($path)) {
            return null;
        }

        $options += ['alias' => Str::slug($path)];
        list($url, $options, $external) = $this->_getCurrentUrlAndOptions($path, $options, $type, $external);

        if (($this->_isOnceIncluded($path, $type, $options)) || $this->_hasAsset($path, $type, $external)) {
            return null;
        }

        unset($options['once']);
        $this->_includedAssets[$type][$path] = true;

        $out = $this->_getTypeOutput($options, $url, $type);

        $options['alias'] = Str::low($options['alias']);
        if ($options['block'] === 'assets') {
            $this->_assets[$type][$options['alias']] = [
                'url'    => $url,
                'output' => $out,
                'path'   => $path,
                'weight' => $options['weight'],
            ];

            return null;
        }

        if (empty($options['block'])) {
            return $out;
        }

        $options = $this->_setFetchBlock($options, $type);
        $this->_View->append($options['block'], $out . $doc->eol);
    }

    /**
     * Check asset on once include.
     *
     * @param string $path
     * @param string $type
     * @param array $options
     * @return bool
     */
    protected function _isOnceIncluded($path, $type, array $options = [])
    {
        return $options['once'] && isset($this->_includedAssets[$type][$path]);
    }

    /**
     * Setup default fetch block if option block is true.
     *
     * @param array $options
     * @param string $block
     * @return array
     */
    protected function _setFetchBlock(array $options, $block)
    {
        if ($options['block'] === true) {
            $options['block'] = $block;
        }

        return $options;
    }
}
