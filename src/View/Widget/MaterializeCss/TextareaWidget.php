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

namespace Core\View\Widget\MaterializeCss;

use JBZoo\Utils\Str;
use Cake\View\Form\ContextInterface;
use Cake\View\Widget\TextareaWidget as CakeTextareaWidget;

/**
 * Class TextareaWidget
 *
 * @package Core\View\Widget\MaterializeCss
 */
class TextareaWidget extends CakeTextareaWidget
{

    /**
     * Render a text area form widget.
     *
     * Data supports the following keys:
     *
     * - `name` - Set the input name.
     * - `val` - A string of the option to mark as selected.
     * - `escape` - Set to false to disable HTML escaping.
     *
     * All other keys will be converted into HTML attributes.
     *
     * @param   array $data The data to build a textarea with.
     * @param   \Cake\View\Form\ContextInterface $context The current form context.
     * @return  string HTML elements.
     */
    public function render(array $data, ContextInterface $context)
    {
        $data = array_merge(['class' => null], $data);

        $data['class'] .= ' materialize-textarea';
        $data['class'] = Str::trim($data['class']);

        return parent::render($data, $context);
    }
}
