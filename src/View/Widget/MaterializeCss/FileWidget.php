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

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\FileWidget as CakeFileWidget;
use JBZoo\Utils\Arr;

/**
 * Class FileWidget
 *
 * @package Core\View\Widget\MaterializeCss
 */
class FileWidget extends CakeFileWidget
{

    /**
     * Render a file upload form widget.
     *
     * Data supports the following keys:
     *
     * - `name` - Set the input name.
     * - `escape` - Set to false to disable HTML escaping.
     *
     * All other keys will be converted into HTML attributes.
     * Unlike other input objects the `val` property will be specifically
     * ignored.
     *
     * @param   array $data The data to build a file input with.
     * @param   \Cake\View\Form\ContextInterface $context The current form context.
     *
     * @return  string HTML elements.
     */
    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'name'         => '',
            'templateVars' => [],
            'escape'       => true
        ];

        unset($data['val']);

        $title = (Arr::key('title', $data)) ? $data['title'] : $data['name'];

        return $this->_templates->format('file', [
            'title'        => $title,
            'name'         => $data['name'],
            'templateVars' => $data['templateVars'],
            'attrs'        => $this->_templates->formatAttributes($data, ['name'])
        ]);
    }
}
