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

namespace Core\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Class PhinxlogFixture
 *
 * @package Core\Test\Fixture
 */
class PhinxlogFixture extends TestFixture
{

    /**
     * Full Table Name
     *
     * @var string
     */
    public $table = 'phinxlog';

    /**
     * Fields of property.
     *
     * @var array
     */
    public $fields = [
        'version'           => ['type' => 'integer'],
        'migration_name'    => ['type' => 'datetime'],
        'start_time'        => ['type' => 'datetime'],
        'end_time'          => ['type' => 'integer'],
        'breakpoint'        => ['type' => 'integer'],
        '_constraints'      => []
    ];
}
