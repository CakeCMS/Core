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

namespace Core\View\Form;

use JBZoo\Data\Data;
use Cake\View\Form\EntityContext as CakeEntityContext;

/**
 * Class EntityContext
 *
 * @package Core\View\Form
 */
class EntityContext extends CakeEntityContext
{

    /**
     * Get the value for a given path.
     *
     * @param string $field
     * @param array $options
     * @return mixed
     */
    public function val($field, $options = [])
    {
        $val = parent::val($field, $options);
        if ($val === null) {
            $parts  = explode('.', $field);
            $entity = $this->entity($parts);

            if ($entity instanceof Data) {
                $key = array_pop($parts);
                $val = $entity->get($key);
            }
        }

        return $val;
    }
}
