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

use Core\Utility\Toolbar;

/**
 * Class ToolbarHelper
 *
 * @package Core\Toolbar
 */
class ToolbarHelper
{

    const ACTION_SAVE = 'save';
    const ACTION_DELETE = 'delete';

    /**
     * Toolbar instance name.
     *
     * @var string
     */
    protected static $_toolbar = Toolbar::DEFAULT_NAME;

    /**
     * Setup toolbar instance name.
     *
     * @param $name
     */
    public static function setToolbar($name)
    {
        self::$_toolbar = $name;
    }

    /**
     * Create link output.
     *
     * @param string $title
     * @param string $url
     * @param array $options
     */
    public static function link($title, $url, array $options = [])
    {
        $toolbar = Toolbar::getInstance(self::$_toolbar);
        $options += [
            'icon' => 'link',
            'button' => 'grey lighten-3'
        ];

        $toolbar->appendButton('Core.link', $title, $url, $options);
    }

    /**
     * Create add link.
     *
     * @param string|null $title
     * @param array|string $url
     * @param string $icon
     * @param array $options
     */
    public static function add($title = null, $url = ['action' => 'add'], $icon = 'plus', array $options = [])
    {
        $toolbar = Toolbar::getInstance(self::$_toolbar);
        $options += [
            'icon' => $icon,
            'button' => 'green lighten-2'
        ];

        $toolbar->appendButton('Core.link', $title, $url, $options);
    }

    /**
     * Delete for process form.
     *
     * @param string|null $title
     */
    public static function delete($title = null)
    {
        $toolbar = Toolbar::getInstance(self::$_toolbar);
        $title   = (empty($title)) ? __d('core', 'Delete') : $title;
        $toolbar->appendButton('Core.action', $title, self::ACTION_DELETE, []);
    }

    /**
     * Save form button.
     *
     * @param null $title
     */
    public static function save($title = null)
    {
        $toolbar = Toolbar::getInstance(self::$_toolbar);
        $title   = (empty($title)) ? __d('core', 'Save') : $title;

        $toolbar->appendButton('Core.action', $title, self::ACTION_SAVE, [
            'button' => 'green lighten-2',
            'icon'   => 'check',
            'class'  => 'jsFormAdd'
        ]);
    }
}
