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

/**
 * Class AssetsHelper
 *
 * @package Core\View\Helper
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class AssetsHelper extends AppHelper
{

    /**
     * Use helpers.
     *
     * @var array
     */
    public $helpers = [
        'Html',
    ];

    /**
     * Default assets options.
     *
     * @var array
     */
    protected $_options = [
        'block'    => true,
        'fullBase' => true,
    ];

    /**
     * Include jquery lib.
     *
     * @return $this
     */
    public function jquery()
    {
        $this->Html->script('libs/jquery.min.js', $this->_options);
        return $this;
    }
}
