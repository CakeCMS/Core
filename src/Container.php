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

namespace Core;

use Pimple\Container as PimpleContainer;

/**
 * Class Container
 *
 * @package Core
 */
class Container extends PimpleContainer
{

    /**
     * Hold CMS instance.
     *
     * @var Cms
     */
    public $app;

    /**
     * Container constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);
        $this->app = Cms::getInstance();
    }
}
