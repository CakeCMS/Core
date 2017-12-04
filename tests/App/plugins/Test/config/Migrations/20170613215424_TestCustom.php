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

use Migrations\AbstractMigration;

/**
 * Class TestCustom
 */
class TestCustom extends AbstractMigration
{

    /**
     * Whether the tables created in this migration
     * should auto-create an `id` field or not
     *
     * This option is global for all tables created in the migration file.
     * If you set it to false, you have to manually add the primary keys for your
     * tables using the Migrations\Table::addPrimaryKey() method
     *
     * @var bool
     */
    public $autoId = false;

    /**
     * Migrate Up.
     *
     * @return void
     * @throws \RuntimeException|\InvalidArgumentException
     */
    public function up()
    {

        $this->table('plugins')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default'       => null,
                'limit'         => 11,
                'null'          => false,
                'signed'        => false
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('name', 'string', [
                'default'   => null,
                'limit'     => 150,
                'null'      => false
            ])
            ->addColumn('slug', 'string', [
                'default'   => null,
                'limit'     => 150,
                'null'      => false
            ])
            ->addColumn('core', 'boolean', [
                'default'   => false,
                'limit'     => null,
                'null'      => false
            ])
            ->addColumn('status', 'boolean', [
                'default'   => false,
                'limit'     => null,
                'null'      => false
            ])
            ->addColumn('params', 'text', [
                'default'   => null,
                'limit'     => null,
                'null'      => true
            ])
            ->create();
    }

    /**
     * Migrate Down.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTable('plugins');
    }
}
