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

namespace Core\View\Helper\Traits;

use JBZoo\Utils\Arr;
use JBZoo\Utils\Str;
use Cake\Utility\Hash;
use Core\View\Helper\HtmlHelper;

/**
 * Class HelperTrait
 *
 * @package     Core\View\Helper\Traits
 * @property    HtmlHelper $Html
 */
trait HelperTrait
{

    /**
     * Adds the given class to the element options
     *
     * @param   array $options Array options/attributes to add a class to
     * @param   string $class The class name being added.
     * @param   string $key the key to use for class.
     * @return  array Array of options with $key set.
     */
    protected function _addClass(array $options = [], $class = null, $key = 'class')
    {
        if (Arr::key($key, $options) && Str::trim($options[$key])) {
            $options[$key] .= ' ' . $class;
        } else {
            $options[$key] = $class;
        }
        return $options;
    }

    /**
     * Get class with union prefix.
     *
     * @param   string $class
     * @return  string
     */
    protected function _class($class = 'cms')
    {
        return $this->_configRead('classPrefix') . '-' . Str::trim(Str::slug($class));
    }

    /**
     * Create current icon.
     *
     * @param   HtmlHelper $html
     * @param   string|int $title
     * @param   array $options
     * @return  array
     */
    protected function _createIcon(HtmlHelper $html, $title, array $options = [])
    {
        list($options, $iconOptions) = $this->_createIconAttr($options);

        if (Arr::key('createIcon', $iconOptions)) {
            unset($iconOptions['createIcon']);
            $title = $html->icon($options['icon'], $iconOptions) . PHP_EOL . $title;
            unset($options['icon']);
        }

        if (Arr::key('iconInline', $options)) {
            unset($options['iconInline']);
        }

        return [$title, $options];
    }

    /**
     * Create icon attributes.
     *
     * @param   array $options
     * @return  array
     */
    protected function _createIconAttr(array $options = [])
    {
        $iconOptions = ['class' => ''];

        if (Arr::key('icon', $options)) {
            if (Arr::key('iconClass', $options)) {
                $iconOptions = $this->_addClass($iconOptions, $options['iconClass']);
                unset($options['iconClass']);
            }

            list ($options, $iconOptions) = $this->_setIconOptions($options, $iconOptions);
        }

        return [$options, $iconOptions];
    }

    /**
     * Create and get button classes.
     *
     * @param   array $options
     * @return  array
     */
    protected function _getBtnClass(array $options = [])
    {
        if (Arr::key('button', $options)) {
            $button = $options['button'];
            unset($options['button']);

            if (is_callable($this->getConfig('prepareBtnClass'))) {
                return (array) call_user_func($this->getConfig('prepareBtnClass'), $this, $options, $button);
            }

            $options = $this->_setBtnClass($button, $options);
        }

        return $options;
    }

    /**
     * Create and get tooltip attributes.
     *
     * @param   array $options
     * @param   string $toggle
     * @return  array
     */
    protected function _getToolTipAttr(array $options = [], $toggle = 'tooltip')
    {
        if (Arr::key('tooltip', $options)) {
            $tooltip = $options['tooltip'];
            unset($options['tooltip']);

            if (is_callable($this->getConfig('prepareTooltip'))) {
                return (array) call_user_func($this->getConfig('prepareTooltip'), $this, $options, $tooltip);
            }

            $_options = [
                'data-toggle'    => $toggle,
                'data-placement' => 'top',
            ];

            if (Arr::key('tooltipPos', $options)) {
                $_options['data-placement'] = (string) $options['tooltipPos'];
                unset($options['tooltipPos']);
            }

            $options = $this->_setTooltipTitle($tooltip, $options);

            return Hash::merge($_options, $options);
        }

        return $options;
    }

    /**
     * Prepare before after content for input container.
     *
     * @param   string|int $type    Set before or after flag.
     * @param   string|int $value
     * @return  null|string
     */
    protected function _prepareBeforeAfterContainer($type, $value)
    {
        $output    = null;
        $iconClass = ($type === 'before') ? 'prefix' : 'postfix';

        if ($value !== null) {
            $output = $value;
            if (is_string($output)) {
                $hasIcon = preg_match('/icon:[a-zA-Z]/', $output);
                if ($hasIcon > 0) {
                    list (, $icon) = explode(':', $output, 2);
                    $icon   = Str::low($icon);
                    $output = $this->Html->icon($icon, ['class' => $iconClass]);
                }
            }
        }

        return $output;
    }

    /**
     * Setup button classes by options.
     *
     * @param   string $button
     * @param   array $options
     * @return  array
     */
    protected function _setBtnClass($button, array $options = [])
    {
        if ($button !== true) {
            $classes = [$this->_configRead('btnPref')];
            foreach ((array) $button as $button) {
                $classes[] = $this->_configRead('btnPref') . '-' . $button;
            }
            $options = $this->_addClass($options, implode(' ', $classes));
        }

        return $options;
    }

    /**
     * Setup icon options.
     *
     * @param   array $options
     * @param   array $iconOptions
     * @return  array
     */
    protected function _setIconOptions(array $options = [], array $iconOptions = [])
    {
        $icon = $options['icon'];
        if (Arr::key('iconInline', $options)) {
            $iconPrefix = $this->_configRead('iconPref');
            $options = $this->_addClass($options, implode(' ', [
                $this->_class('icon'),
                $iconPrefix,
                $iconPrefix . '-' . $icon
            ]));

            unset($options['icon']);
        } else {
            $options['escape'] = false;
            $iconOptions['createIcon'] = true;
        }

        return [$options, $iconOptions];
    }

    /**
     * Setup tooltip title by options.
     *
     * @param   string $tooltip
     * @param   array $options
     * @return  array
     */
    protected function _setTooltipTitle($tooltip, array $options = [])
    {
        if ($tooltip === true && !Arr::key('title', $options)) {
            $options['title'] = strip_tags($options['label']);
        }

        if (is_string($tooltip)) {
            $options['title'] = $tooltip;
        }

        return $options;
    }
}
