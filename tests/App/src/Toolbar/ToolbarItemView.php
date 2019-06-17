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

namespace TestApp\Toolbar;

use Core\Toolbar\ToolbarItem;

/**
 * Class ToolbarItemView
 *
 * @package Test\App\Toolbar
 */
class ToolbarItemView extends ToolbarItem
{

    /**
     * Get the button output.
     *
     * @return string
     */
    public function fetchItem()
    {
        return 'view';
    }
}
