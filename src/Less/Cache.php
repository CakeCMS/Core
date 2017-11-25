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

namespace Core\Less;

use JBZoo\Utils\FS;
use JBZoo\Utils\Str;
use JBZoo\Utils\Slug;
use JBZoo\Less\Cache as JCache;

/**
 * Class Cache
 *
 * @package Union\Core\Lib\Less
 */
class Cache extends JCache
{

    /**
     * Get result full file path.
     *
     * @return string
     */
    protected function _getResultFile()
    {
        //  Normalize relative path
        $relPath = Slug::filter($this->_hash, '_');
        $relPath = Str::low($relPath);

        if (!$this->_options->get('debug')) {
            $relPath .= '.min';
        }

        //  Get full clean path
        $fullPath = FS::real($this->_options->get('cache_path')) . '/' . $relPath . '.css';
        $fullPath = FS::clean($fullPath);

        return $fullPath;
    }
}
