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
 * Class PagesFixture
 *
 * @package Core\Test\Fixture
 */
class PagesFixture extends TestFixture
{

    /**
     * Full Table Name
     *
     * @var string
     */
    public $table = 'pages';

    /**
     * Fields of property.
     *
     * @var array
     */
    public $fields = [
        'id'           => ['type' => 'integer'],
        'title'        => ['type' => 'string'],
        'alias'        => ['type' => 'string'],
        'status'       => ['type' => 'integer'],
        'params'       => 'text',
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

    /**
     * PagesFixture constructor.
     */
    public function __construct()
    {
        $this->records = [
            [
                'id'        => 1,
                'title'     => 'Page 1',
                'alias'     => 'page-1',
                'status'    => 1,
                'params'    => json_encode([], JSON_PRETTY_PRINT),
            ],
            [
                'id'        => 2,
                'title'     => 'Test page',
                'alias'     => 'test-page',
                'status'    => 1,
                'params'    => json_encode([], JSON_PRETTY_PRINT),
            ],
            [
                'id'        => 3,
                'title'     => 'Custom page',
                'alias'     => 'custom-page',
                'status'    => 1,
                'params'    => json_encode([], JSON_PRETTY_PRINT),
            ],
            [
                'id'        => 4,
                'title'     => 'Un publish',
                'alias'     => 'un-publish',
                'status'    => 0,
                'params'    => json_encode([], JSON_PRETTY_PRINT),
            ],
        ];

        parent::__construct();
    }
}
