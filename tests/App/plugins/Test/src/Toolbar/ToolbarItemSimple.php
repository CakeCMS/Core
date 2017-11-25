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

namespace Test\Toolbar;

use Core\Toolbar\ToolbarItem;

/**
 * Class ToolbarItemSimple
 *
 * @package Test\Toolbar
 */
class ToolbarItemSimple extends ToolbarItem
{

    /**
     * Fetch the HTML for the button.
     *
     * @return string
     */
    public function fetchItem()
    {
        list($type) = func_get_args();
        return $type;
    }
}
