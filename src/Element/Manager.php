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

namespace Core\Element;

use JBZoo\Utils\FS;
use JBZoo\Utils\Str;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Cake\Core\Configure;
use Cake\Core\ClassLoader;
use Core\ORM\Entity\Element as EntityElement;
use Core\Element\Exception\ElementException;

/**
 * Class Manager
 *
 * @package Core\Element
 */
class Manager
{

    const DEFAULT_GROUP = 'Item';

    /**
     * Create element instance.
     *
     * @param string $type
     * @param string $group
     * @param array $config
     * @param EntityElement|null $entity
     * @return Element
     * @throws ElementException|\JBZoo\Utils\Exception
     */
    public function create($type, $group = self::DEFAULT_GROUP, array $config = [], EntityElement $entity = null)
    {
        $type = $this->_normalize($type);
        if (!$type) {
            throw new ElementException(__d('core_dev', 'Element type "{0}" is empty', $type));
        }

        $group = $this->_normalize($group);
        if (!$group) {
            throw new ElementException(__d('core_dev', 'Element group "{0}" is empty', $group));
        }

        $this->_findElement($group, $type);
        $className = $this->_getClassName($group, $type);

        if (!class_exists($className)) {
            throw new ElementException(__d('core_dev', 'Element class "{0}" not found!', $className));
        }

        /** @var Element $element */
        $element = new $className($type, $group);

        $config = Hash::merge($config, [
            'id'          => $element->isCore() ? '_' . Str::low($type) : Text::uuid(),
            'name'        => $element->getName(),
            'type'        => $type,
            'group'       => $group,
            'description' => '',
        ]);

        $element->id = $config['id'];
        $element->setConfig($config);

        if ($entity !== null) {
            $element->setEntity($entity);
        }

        $element->initialize();

        return $element;
    }

    /**
     * Find and load element.
     *
     * @param string $group
     * @param string $type
     * @return bool|string
     */
    protected function _findElement($group, $type)
    {
        $className = $this->_getClassName($group, $type);

        if (!class_exists($className)) {
            $loader = new ClassLoader();
            $paths  = Configure::read('App.paths.elements');
            foreach ($paths as $path) {
                $path = FS::clean($path . '/' . $group . '/' . $type, '/');
                $loader->addNamespace("\\{$group}\\{$type}", $path);
                $result = $loader->loadClass("{$group}\\{$type}\\{$type}Element");
                if ($result !== false) {
                    return $result;
                }
            }
        }

        return false;
    }

    /**
     * Get current class name.
     *
     * @param string $group
     * @param string $type
     * @return string
     */
    protected function _getClassName($group, $type)
    {
        return "\\Elements\\{$group}\\{$type}Element";
    }

    /**
     * Normalize string format.
     *
     * @param string $string
     * @return string
     */
    protected function _normalize($string)
    {
        return ucfirst(Str::low($string));
    }
}
