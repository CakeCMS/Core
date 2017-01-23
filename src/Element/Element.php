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
use JBZoo\Data\Data;
use JBZoo\Data\JSON;
use JBZoo\Data\PHPArray;
use Cake\Core\Configure;
use Core\ORM\Entity\Element as EntityElement;

/**
 * Class Element
 *
 * @package Core\Element
 */
abstract class Element
{

    const MANIFEST_FILE = 'element.php';

    /**
     * @var Data
     */
    public $config;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    protected $_elementGroup;

    /**
     * @var string
     */
    protected $_elementType;

    /**
     * @var EntityElement
     */
    protected $_entity;

    /**
     * @var PHPArray
     */
    protected $_meta;

    /**
     * Element constructor.
     *
     * @param string $type
     * @param string $group
     */
    public function __construct($type, $group)
    {
        $this->_elementType  = Str::low($type);
        $this->_elementGroup = Str::low($group);
    }

    /**
     * Get entity object.
     *
     * @return EntityElement
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Get meta data by key.
     *
     * @param string|int $key
     * @param mixed $default
     * @param mixed $filter
     * @return mixed
     * @throws \JBZoo\Utils\Exception
     */
    public function getMetaData($key, $default = null, $filter = null)
    {
        return $this->loadMeta()->find('meta.' . $key, $default, $filter);
    }

    /**
     * Get element name.
     *
     * @return null|string
     * @throws \JBZoo\Utils\Exception
     */
    public function getName()
    {
        return $this->getMetaData('name');
    }

    /**
     * Get element path.
     *
     * @return bool|string
     */
    public function getPath()
    {
        $typeDir  = ucfirst($this->_elementType);
        $groupDir = ucfirst($this->_elementGroup);

        foreach ($this->_getPaths() as $path) {
            $fullPath = FS::clean($path . '/' . $groupDir . '/' . $typeDir, '/');
            if (FS::isDir($fullPath)) {
                return $fullPath;
            }
        }

        return false;
    }

    /**
     * On initialize element.
     *
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * Check is core element.
     *
     * @return bool
     * @throws \JBZoo\Utils\Exception
     */
    public function isCore()
    {
        return $this->getMetaData('core', false, 'bool');
    }

    /**
     * Load element meta data.
     *
     * @return PHPArray
     */
    public function loadMeta()
    {
        if (!$this->_meta) {
            $this->_meta = new PHPArray($this->getPath() . '/' . self::MANIFEST_FILE);
        }

        return $this->_meta;
    }

    /**
     * Setup element config data.
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = new JSON($config);
    }

    /**
     * Setup entity object.
     *
     * @param EntityElement $entity
     * @return void
     */
    public function setEntity(EntityElement $entity)
    {
        $this->_entity = $entity;
    }

    /**
     * Get element paths.
     *
     * @return array
     */
    protected function _getPaths()
    {
        return (array) Configure::read('App.paths.elements');
    }
}
