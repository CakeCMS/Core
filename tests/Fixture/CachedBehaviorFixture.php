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

namespace Core\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Class CachedBehaviorFixture
 *
 * @package Core\Test\TestCase\Fixture
 */
class CachedBehaviorFixture extends TestFixture
{

    /**
     * Full Table Name
     *
     * @var string
     */
    public $table = 'cached_behavior';

    /**
     * Fields property.
     *
     * @var array
     */
    public $fields = [
        'id'           => ['type' => 'integer'],
        'title'        => ['type' => 'string'],
        'alias'        => ['type' => 'string'],
        'status'       => ['type' => 'integer'],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

    /**
     * Records property.
     *
     * @var array
     */
    public $records = [
        [
            'id'     => 1,
            'title'  => 'Record 1',
            'alias'  => 'record-1',
            'status' => 1,
        ],
        [
            'id'     => 2,
            'title'  => 'Record 2',
            'alias'  => 'record-2',
            'status' => 1,
        ],
        [
            'id'     => 3,
            'title'  => 'Record 3',
            'alias'  => 'record-3',
            'status' => 1,
        ],
        [
            'id'     => 4,
            'title'  => 'Record 4',
            'alias'  => 'record-4',
            'status' => 0,
        ],
    ];
}
