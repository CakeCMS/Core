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

namespace Core\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\BasicWidget as BaseBasicWidget;

/**
 * Class BasicWidget
 *
 * @package Core\View\Widget
 */
class BasicWidget extends BaseBasicWidget
{


    /**
     * Render a text widget or other simple widget like email/tel/number.
     *
     * This method accepts a number of keys:
     *
     * - `name` The name attribute.
     * - `val` The value attribute.
     * - `escape` Set to false to disable escaping on all attributes.
     *
     * Any other keys provided in $data will be converted into HTML attributes.
     *
     * @param   array $data The data to build an input with.
     * @param   \Cake\View\Form\ContextInterface $context The current form context.
     * @return  string
     */
    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'name'          => '',
            'val'           => null,
            'type'          => 'text',
            'escape'        => true,
            'templateVars'  => []
        ];
        $data['value'] = $data['val'];
        unset($data['val']);

        return $this->_templates->format('input', [
            'name'          => $data['name'],
            'type'          => $data['type'],
            'templateVars'  => $data['templateVars'],
            'attrs'         => $this->_templates->formatAttributes(
                $data,
                ['name', 'type', 'before', 'after']
            ),
        ]);
    }
}
