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
 * Class MovesFixture
 *
 * @package Core\Test\Fixture
 */
class MovesFixture extends TestFixture
{

    /**
     * Full Table Name
     *
     * @var string
     */
    public $table = 'moves';

    /**
     * Fields / Schema for the fixture.
     *
     * @var array
     */
    public $fields = [
        'id'           => ['type' => 'integer'],
        'parent_id'    => ['type' => 'integer', 'null' => true],
        'name'         => ['type' => 'string'],
        'slug'         => ['type' => 'string'],
        'params'       => 'text',
        'lft'          => ['type' => 'integer'],
        'rght'         => ['type' => 'integer'],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

    /**
     * GroupsFixture constructor.
     */
    public function __construct()
    {
        $this->records = [
            [
                'id'        => 1,
                'parent_id' => null,
                'name'      => 'Public',
                'slug'      => 'public',
                'params'    => json_encode([

                ], JSON_PRETTY_PRINT),
                'lft'       => 1,
                'rght'      => 6
            ],
            [
                'id'        => 2,
                'parent_id' => 1,
                'name'      => 'Registered',
                'slug'      => 'registered',
                'params'    => json_encode([

                ], JSON_PRETTY_PRINT),
                'lft'       => 2,
                'rght'      => 3
            ],
            [
                'id'        => 3,
                'parent_id' => 1,
                'name'      => 'Admin',
                'slug'      => 'admin',
                'params'    => json_encode([

                ], JSON_PRETTY_PRINT),
                'lft'       => 4,
                'rght'      => 5
            ]
        ];

        parent::__construct();
    }
}
