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

use Cake\Utility\Hash;

/**
 * Class FilterHelper
 *
 * @package Core\View\Helper
 */
class FilterHelper extends AppHelper
{

    const DEFAULT_ELEMENT = 'Core.filter';

    /**
     * Render filter element.
     *
     * @param string $model
     * @param array $fields
     * @param string $element
     * @param array $data
     * @return string
     */
    public function render($model, array $fields = [], $element = self::DEFAULT_ELEMENT, array $data = [])
    {
        $data = Hash::merge([
            'clearUrl'   => [],
            'model'      => $model,
            'formFields' => $fields,
        ], $data);

        return $this->_View->element($element, $data);
    }
}
