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
use Cake\View\Helper;
use Cake\Utility\Hash;

/**
 * Trait MaterializeCssTrait
 *
 * @package Core\View\Helper\Traits
 */
trait MaterializeCssTrait
{

    /**
     * Setup tooltip data-tooltip attr.
     *
     * @param   array $options
     * @param   string $tooltip
     * @return  array
     */
    protected function _dataTooltip(array $options, $tooltip)
    {
        if (Arr::key('title', $options)) {
            $options['data-tooltip'] = $options['title'];
        }

        if (is_string($tooltip)) {
            $options['data-tooltip'] = $tooltip;
        }

        return $options;
    }

    /**
     * Prepare form buttons.
     *
     * @param   Helper $helper
     * @param   array $options
     * @param   string $button
     * @return  array
     */
    protected function _prepareBtn(Helper $helper, array $options, $button)
    {
        $options = $helper->addClass($options, 'waves-effect waves-light btn');
        if (!empty($button)) {
            $options = $helper->addClass($options, Str::trim((string) $button));
        }

        return $options;
    }

    /**
     * Prepare tooltip attrs.
     *
     * @param Helper $helper
     * @param array $options
     * @param string $tooltip
     * @return array
     */
    protected function _prepareTooltip(Helper $helper, array $options, $tooltip)
    {
        $_options = [
            'data-position' => 'top'
        ];

        if (Arr::key('tooltipPos', $options)) {
            $_options['data-position'] = (string) $options['tooltipPos'];
            unset($options['tooltipPos']);
        }

        $options = $this->_tooltipTitle($options, $tooltip);
        $options = $this->_dataTooltip($options, $tooltip);
        $options = $helper->addClass($options, 'hasTooltip');

        return Hash::merge($_options, $options);
    }

    /**
     * Setup tooltip title.
     *
     * @param array $options
     * @param string $tooltip
     * @return array
     */
    protected function _tooltipTitle(array $options, $tooltip)
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
