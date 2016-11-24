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

namespace Core\Event;

use Cake\Event\Event as CakeEvent;

use JBZoo\Data\Data;

/**
 * Class Event
 *
 * @package Core\Event
 */
class Event extends CakeEvent
{

    /**
     * Custom data for the method that receives the event
     *
     * @var Data
     */
    public $data;

    /**
     * Event constructor.
     *
     * @param string $name
     * @param null|string $subject
     * @param null|array $data
     */
    public function __construct($name, $subject = null, $data = null)
    {
        parent::__construct($name, $subject, $data);
        $this->data = new Data($data);
    }

    /**
     * Access the event data/payload.
     *
     * @return array
     */
    public function data()
    {
        return $this->data->getArrayCopy();
    }
}
