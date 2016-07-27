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

namespace Core\View;

use Core\Plugin;
use Cake\View\View;
use Cake\Event\Event;

/**
 * Class AppView
 *
 * @package Core\View
 */
class AppView extends View
{

    /**
     * Initialization hook method.
     *
     * Properties like $helpers etc. cannot be initialized statically in your custom
     * view class as they are overwritten by values from controller in constructor.
     * So this method allows you to manipulate them as required after view instance
     * is constructed.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        Plugin::manifestEvent('View.initialize', $this);
    }

    /**
     * Is called before each view file is rendered. This includes elements, views, parent views and layouts.
     *
     * @param Event $event
     * @param string $viewFile
     * @return void
     */
    public function beforeRenderFile(Event $event, $viewFile)
    {
        Plugin::manifestEvent('View.beforeRenderFile', $this, $event, $viewFile);
    }

    /**
     * Is called after each view file is rendered. This includes elements, views, parent views and layouts.
     * A callback can modify and return $content to change how the rendered content will be displayed in the browser.
     *
     * @param Event $event
     * @param string $viewFile
     * @param string $content
     * @return void
     */
    public function afterRenderFile(Event $event, $viewFile, $content)
    {
        Plugin::manifestEvent('View.afterRenderFile', $this, $event, $viewFile, $content);
    }

    /**
     * Is called after the controller’s beforeRender method but before the controller renders view and layout.
     * Receives the file being rendered as an argument.
     *
     * @param Event $event
     * @param string $viewFile
     * @return void
     */
    public function beforeRender(Event $event, $viewFile)
    {
        Plugin::manifestEvent('View.beforeRender', $this, $event, $viewFile);
    }

    /**
     * Is called after the view has been rendered but before layout rendering has started.
     *
     * @param Event $event
     * @param string $viewFile
     * @return void
     */
    public function afterRender(Event $event, $viewFile)
    {
        Plugin::manifestEvent('View.afterRender', $this, $event, $viewFile);
    }

    /**
     * Is called before layout rendering starts. Receives the layout filename as an argument.
     *
     * @param Event $event
     * @param string $layoutFile
     * @return void
     */
    public function beforeLayout(Event $event, $layoutFile)
    {
        Plugin::manifestEvent('View.beforeLayout', $this, $event, $layoutFile);
    }

    /**
     * Is called after layout rendering is complete. Receives the layout filename as an argument.
     *
     * @param Event $event
     * @param string $layoutFile
     * @return void
     */
    public function afterLayout(Event $event, $layoutFile)
    {
        Plugin::manifestEvent('View.beforeLayout', $this, $event, $layoutFile);
    }
}
