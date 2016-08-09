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

namespace Core\Lib\Less;

use JBZoo\Utils\FS;
use JBZoo\Less\Exception;
use JBZoo\Less\Less as JBLess;

/**
 * Class Less
 *
 * @package Core\Lib\Less
 */
class Less extends JBLess
{

    /**
     * Compile less file.
     *
     * @param string $lessFile
     * @param null|string $basePath
     * @return array
     * @throws Exception
     */
    public function compile($lessFile, $basePath = null)
    {
        try {
            $basePath = $this->_prepareBasepath($basePath, dirname($lessFile));

            $cache = new Cache($this->_options);
            $cache->setFile($lessFile, $basePath);

            $isExpired = $cache->isExpired();
            $isForce   = $this->_options->get('force', false, 'bool');

            if ($isForce || $cache->isExpired()) {
                $result = $this->_driver->compile($lessFile, $basePath);
                $cache->save($result);
            }

            $isExpired = ($isForce) ? true : $isExpired;
            $return = [FS::clean($cache->getFile(), '/'), $isExpired];

        } catch (\Exception $e) {
            $message = 'Less error: ' . $e->getMessage();
            throw new Exception($message);
        }

        return $return;
    }
}
