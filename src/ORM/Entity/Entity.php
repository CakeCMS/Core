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

use Core\Cms;
use JBZoo\Data\Data;
use JBZoo\Data\JSON;
use Cake\ORM\Entity as CakeEntity;

/**
 * Class Entity
 *
 * @package Core\ORM\Entity
 * @property Data $params
 */
class Entity extends CakeEntity
{

    /**
     * Hold CMS object.
     *
     * @var Cms
     */
    public $cms;

    /**
     * Entity constructor.
     *
     * @param array $properties
     * @param array $options
     */
    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);
        $this->cms = Cms::getInstance();
    }

    /**
     * Create new current params field.
     *
     * @param $params
     * @return JSON
     */
    protected function _getParams($params)
    {
        return new JSON($params);
    }
}
