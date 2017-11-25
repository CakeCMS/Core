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

namespace Core\ORM;

use JBZoo\Data\JSON;
use Cake\Event\Event;
use Cake\Database\Type;
use Cake\ORM\Table as CakeTable;

/**
 * Class Table
 *
 * @package Core\ORM
 */
class Table extends CakeTable
{

    /**
     * Callback before request data is converted into entities.
     *
     * @param Event $event
     * @param \ArrayObject $data
     * @param \ArrayObject $options
     * @return void
     * @SuppressWarnings("unused")
     */
    public function beforeMarshal(Event $event, \ArrayObject $data, \ArrayObject $options)
    {
        $this->_prepareParamsData($data);
    }

    /**
     * Prepare params data.
     *
     * @param \ArrayObject $data
     * @return void
     */
    protected function _prepareParamsData(\ArrayObject $data)
    {
        if (isset($data['params'])) {
            $params = new JSON((array) $data['params']);
            $data->offsetSet('params', $params);
        }
    }
}
