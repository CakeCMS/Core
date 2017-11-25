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

namespace Core\Toolbar;

use Cake\Routing\Router;

/**
 * Class ToolbarItemLink
 *
 * @package Core\Toolbar
 */
class ToolbarItemLink extends ToolbarItem
{

    /**
     * Fetch button id.
     *
     * @return string
     * @SuppressWarnings("unused")
     */
    public function fetchItem()
    {
        list ($source, $title, $url, $options) = func_get_args();
        $url = Router::url($url);

        return $this->_view->Html->link($title, $url, $options);
    }
}
