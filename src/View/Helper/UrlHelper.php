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

use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Helper\UrlHelper as CakeUrlHelper;

/**
 * Class UrlHelper
 *
 * @package Core\View\Helper
 */
class UrlHelper extends CakeUrlHelper
{
    public function assetUrl($path, array $options = [])
    {
        dump('---------');
        dump($path);
        if (is_array($path)) {
            return $this->build($path, !empty($options['fullBase']));
        }
        if (strpos($path, '://') !== false || preg_match('/^[a-z]+:/i', $path)) {
            return $path;
        }
        if (!array_key_exists('plugin', $options) || $options['plugin'] !== false) {
            list($plugin, $path) = $this->_View->pluginSplit($path, false);
            dump($plugin . ':' . $path);
        }
        if (!empty($options['pathPrefix']) && $path[0] !== '/') {
            $path = $options['pathPrefix'] . $path;
        }
        if (!empty($options['ext']) &&
            strpos($path, '?') === false &&
            substr($path, -strlen($options['ext'])) !== $options['ext']
        ) {
            $path .= $options['ext'];
        }
        if (preg_match('|^([a-z0-9]+:)?//|', $path)) {
            return $path;
        }

        if (isset($plugin)) {

            if ($plugin == 'Test') {
                dump($plugin);
                dump($path);
                dump(Inflector::underscore($plugin));
            }

            $path = Inflector::underscore($plugin) . '/' . $path;

            if ($plugin == 'Test') {
                dump($path);
            }
        }
        dump($path);
        $path = $this->_encodeUrl($this->assetTimestamp($this->webroot($path)));
        dump($path);

        if (!empty($options['fullBase'])) {
            $path = rtrim(Router::fullBaseUrl(), '/') . '/' . ltrim($path, '/');
        }

        return $path;
    }
}