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

namespace Core\Path;

use Cake\Core\Configure;
use JBZoo\Utils\FS;
use JBZoo\Utils\Arr;
use JBZoo\Path\Exception;
use JBZoo\Path\Path as JBPAth;

/**
 * Class Path
 *
 * @package Core\Path
 */
class Path extends JBPAth
{

    const LS_MODE_DIR = 'dir';
    const LS_MODE_FILE = 'file';

    /**
     * Flag of result path (If true, is real path. If false, is relative path).
     *
     * @var string
     */
    protected $_isReal = false;

    /**
     * Get a list of directories from a resource.
     *
     * @param string $resource
     * @param bool $recursive
     * @param null $filter
     * @return mixed
     */
    public function dirs($resource, $recursive = false, $filter = null)
    {
        return $this->ls($resource, self::LS_MODE_DIR, $recursive, $filter);
    }

    /**
     * Get url to a file.
     *
     * @param string $source
     * @param bool $full
     * @return null|string
     */
    public function url($source, $full = true)
    {
        $stamp = Configure::read('Asset.timestamp');
        $url = parent::url($source, $full);

        if ($url !== null && $stamp && strpos($url, '?') === false) {
            $fullPath = $this->get($source);
            $url .= '?' . @filemtime($fullPath);
        }

        return $url;
    }

    /**
     * Get path instance.
     *
     * @param string $key
     * @return Path
     * @throws Exception
     */
    public static function getInstance($key = 'default')
    {
        if ((string) $key = '') {
            throw new Exception('Invalid object key');
        }

        if (!Arr::key($key, self::$_objects)) {
            self::$_objects[$key] = new self();
        }

        return self::$_objects[$key];
    }

    /**
     * Get a list of files or diretories from a resource.
     *
     * @param string $resource
     * @param string $mode
     * @param bool $recursive
     * @param null $filter
     * @return array
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function ls($resource, $mode = self::LS_MODE_FILE, $recursive = false, $filter = null)
    {
        $files = [];
        list (, $paths, $path) = $this->_parse($resource);

        foreach ((array) $paths as $_path) {
            if (file_exists($_path . '/' . $path)) {
                foreach ($this->_list(FS::clean($_path . '/' . $path), '', $mode, $recursive, $filter) as $file) {
                    if (!Arr::in($file, $files)) {
                        $files[] = $file;
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Get the list of files or directories in a given path.
     *
     * @param string $path
     * @param string $prefix
     * @param string $mode
     * @param bool $recursive
     * @param null $filter
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _list($path, $prefix = '', $mode = 'file', $recursive = false, $filter = null)
    {
        $files  = [];
        $ignore = ['.', '..', '.DS_Store', '.svn', '.git', '.gitignore', '.gitmodules', 'cgi-bin'];

        if ($scan = @scandir($path)) {
            foreach ($scan as $file) {
                // continue if ignore match
                if (Arr::in($file, $ignore)) {
                    continue;
                }

                if (is_dir($path . '/' . $file)) {
                    // add dir
                    if ($mode === 'dir') {
                        // continue if no regex filter match
                        if ($filter && !preg_match($filter, $file)) {
                            continue;
                        }

                        $files[] = $prefix . $file;
                    }

                    // continue if not recursive
                    if (!$recursive) {
                        continue;
                    }

                    // read subdirectory
                    $files = array_merge(
                        $files,
                        $this->_list($path . '/' . $file, $prefix . $file . '/', $mode, $recursive, $filter)
                    );

                } else {
                    // add file
                    if ($mode === 'file') {
                        // continue if no regex filter match
                        if ($filter && !preg_match($filter, $file)) {
                            continue;
                        }

                        $files[] = $prefix.$file;
                    }
                }
            }
        }

        return $files;
    }
}
