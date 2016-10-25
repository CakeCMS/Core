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

namespace Core\Toolbar;

use Core\Utility\Toolbar;

/**
 * Class ToolbarHelper
 *
 * @package Core\Toolbar
 */
class ToolbarHelper
{

    /**
     * Create link output.
     *
     * @param string $title
     * @param string $url
     * @param array $options
     */
    public static function link($title, $url, array $options = [])
    {
        $toolbar = Toolbar::getInstance();
        $options += [
            'icon'   => 'link',
            'button' => 'grey lighten-3'
        ];

        $toolbar->appendButton('Core.link', $title, $url, $options);
    }
}
