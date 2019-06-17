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

namespace Core\ORM\Entity;

use JBZoo\Data\Data;
use JBZoo\Data\JSON;
use Cake\ORM\Entity as CakeEntity;

/**
 * Class Entity
 *
 * @property    Data $params
 *
 * @package     Core\ORM\Entity
 */
class Entity extends CakeEntity
{

    /**
     * Create new current params field.
     *
     * @param   $params
     *
     * @return  JSON
     */
    protected function _getParams($params)
    {
        return new JSON($params);
    }
}
