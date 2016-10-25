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

use JBZoo\Utils\Str;
use Core\Utility\Toolbar;
use Core\View\ButtonView;
use Cake\Utility\Inflector;

/**
 * Class ToolbarItem
 *
 * @package Core\Toolbar
 */
abstract class ToolbarItem
{

    /**
     * Reference to the object that instantiated the element.
     *
     * @var Toolbar|null
     */
    protected $_parent = null;

    /**
     * Button view object.
     *
     * @var ButtonView
     */
    protected $_view;

    /**
     * ToolbarButton constructor.
     *
     * @param null|Toolbar $parent
     */
    public function __construct($parent = null)
    {
        $this->_parent = $parent;
        $this->_view   = new ButtonView();
    }

    /**
     * Get the item output.
     *
     * @return string
     */
    abstract public function fetchItem();

    /**
     * Fetch button id.
     *
     * @param string $type
     * @param string $name
     * @return string
     * @SuppressWarnings("unused")
     */
    public function fetchId($type, $name)
    {
        return Inflector::dasherize($this->_parent->getName()) . '-' . Str::slug($type);
    }

    /**
     * Render toolbar html.
     *
     * @param array $node
     * @return string
     */
    public function render(&$node)
    {
        $id = call_user_func_array([&$this, 'fetchId'], $node);
        $output = call_user_func_array([&$this, 'fetchItem'], $node);
        list ($source) = $node;
        list ($plugin) = pluginSplit($source);

        $options = [
            'id'     => $id,
            'output' => $output,
            'class'  => $node['class'],
        ];

        $element = 'Toolbar/wrapper';
        if ($plugin !== null) {
            $element = $plugin . '.' . $element;
        }

        return $this->_view->element($element, $options);
    }
}
