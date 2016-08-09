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

use Cake\View\View;
use Cake\View\Helper\HtmlHelper as CakeHtmlHelper;

/**
 * Class HtmlHelper
 *
 * @package Core\View\Helper
 */
class HtmlHelper extends CakeHtmlHelper
{

    /**
     * HtmlHelper constructor.
     *
     * @param View $View
     * @param array $config
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
        $this->_configWrite('templates.icon', '<i class="{{class}}"{{attrs}}></i>');
    }
}
