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

namespace Core\Migration;

use Core\Plugin;
use JBZoo\Utils\Str;
use Phinx\Util\Util;
use JBZoo\Data\Data;
use JBZoo\Utils\Arr;
use Phinx\Config\Config;
use Cake\Core\Configure;
use Migrations\CakeAdapter;
use Cake\Database\Connection;
use Phinx\Db\Adapter\AdapterFactory;
use Phinx\Migration\AbstractMigration;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\MissingPluginException;

/**
 * Class Manager
 *
 * @package Core\Migration
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Manager
{

    const SCHEMA_TABLE = 'phinxlog';

    /**
     * Phinx data base adapter.
     *
     * @var \Phinx\Db\Adapter\AdapterInterface
     */
    protected $_adapter;

    /**
     * DB adapter name.
     *
     * @var string
     */
    protected $_adapterName;

    /**
     * Phinx configuration.
     *
     * @var Config
     */
    protected $_config;

    /**
     * Hold connection config.
     *
     * @var array|Data
     */
    protected $_connectionConfig = [];

    /**
     * DB connection name.
     *
     * @var string
     */
    protected $_connectionName;

    /**
     * Hold migrations.
     *
     * @var array
     */
    protected $_migrations = [];

    /**
     * Migration plugin name.
     *
     * @var string
     */
    protected $_plugin;

    /**
     * Manager constructor.
     *
     * @param string $plugin
     * @throws MissingPluginException
     */
    public function __construct($plugin)
    {
        if (Plugin::loaded($plugin)) {
            $connectConfigure        = ConnectionManager::configured();
            $this->_connectionName   = array_shift($connectConfigure);
            $this->_connectionConfig = new Data(ConnectionManager::getConfig($this->_connectionName));
            $this->_adapterName      = $this->_getAdapterName($this->_connectionConfig->get('driver'));

            $config = [
                'paths' => [
                    'migrations' => Migration::getPath($plugin),
                ],
                'environments' => $this->_configuration()
            ];

            $this->_plugin  = $plugin;
            $this->_config  = new Config($config);
            $adapterOptions = $this->_config->getEnvironment($this->_connectionName);
            $this->_adapter = $this->_setAdapter($adapterOptions);
        } else {
            throw new MissingPluginException($plugin);
        }
    }

    /**
     * Get plugin migrations.
     *
     * @return array
     */
    public function getMigrations()
    {
        if (count($this->_migrations) <= 0) {
            $versions = [];
            $paths = $this->_config->getMigrationPaths();
            foreach ($paths as $path) {
                $files = glob($path . '/*.php');
                foreach ($files as $file) {
                    $fileName = basename($file);
                    if (Util::isValidMigrationFileName($fileName)) {
                        $version = Util::getVersionFromFileName($fileName);
                        if (Arr::key($version, $versions)) {
                            throw new \InvalidArgumentException(
                                __d(
                                    'core',
                                    'Duplicate migration - "%s" has the same version as "%s"',
                                    $file,
                                    $versions[$version]->getVersion()
                                )
                            );
                        }

                        //  Convert the filename to a class name.
                        $class = Util::mapFileNameToClassName($fileName);

                        /** @noinspection PhpIncludeInspection */
                        require_once $file;
                        if (!class_exists($class)) {
                            throw new \InvalidArgumentException(
                                __d('core', 'Could not find class "%s" in file "%s"', $class, $file)
                            );
                        }

                        //  Instantiate migration class.
                        $migration = new $class($version);
                        if (!($migration instanceof AbstractMigration)) {
                            throw new \InvalidArgumentException(
                                __d(
                                    'core',
                                    'The class "%s" in file "%s" must extend \Phinx\Migration\AbstractMigration',
                                    $class,
                                    $file
                                )
                            );
                        }

                        $versions[$version] = $migration;
                    }
                }
            }

            ksort($versions);
            $this->_migrations = $versions;
        }

        return $this->_migrations;
    }

    /**
     * Checks if the migration with version number $version as already been mark migrated
     *
     * @param int|string $version Version number of the migration to check
     * @return bool
     */
    public function isMigrated($version)
    {
        return Arr::in($version, $this->_adapter->getVersions());
    }

    /**
     * Migrate up action.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function migrateUp()
    {
        $output = [];
        foreach ($this->getMigrations() as $version => $migration) {
            if ($this->isMigrated($version) === false) {
                $paths = $this->_config->getMigrationPaths();
                foreach ($paths as $path) {
                    $migrationFile = glob($path . DS . $version . '*');
                    $filePath      = array_shift($migrationFile);
                    $className     = Util::mapFileNameToClassName(basename($filePath));

                    /** @noinspection PhpIncludeInspection */
                    require_once $filePath;

                    /** @var AbstractMigration $migrate */
                    $migrate  = new $className($version);
                    $versions = $this->_adapter->getVersions();

                    //  Check migrate value version.
                    if ($migrate->getVersion() > $version) {
                        $output[] = __d(
                            'core',
                            'The version «{0}» is lower than the current.',
                            sprintf('<strong>%s</strong>', $version)
                        );
                    }

                    if (!Arr::in($version, $versions)) {
                        $migrate->setAdapter($this->_adapter)->up();
                        $output[] = $this->_execute($migrate);
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Setup Phinx configuration.
     *
     * @return array
     */
    protected function _configuration()
    {
        return [
            'default_migration_table' => self::SCHEMA_TABLE,
            'default_database'        => $this->_connectionConfig->get('name'),

            $this->_connectionConfig->get('name') => [
                'adapter'       => $this->_adapterName,
                'version_order' => Config::VERSION_ORDER_CREATION_TIME,
                'host'          => $this->_connectionConfig->get('host'),
                'port'          => $this->_connectionConfig->get('port'),
                'name'          => $this->_connectionConfig->get('database'),
                'user'          => $this->_connectionConfig->get('username'),
                'pass'          => $this->_connectionConfig->get('password'),
                'charset'       => $this->_connectionConfig->get('encoding'),
                'unix_socket'   => $this->_connectionConfig->get('unix_socket')
            ]
        ];
    }

    /**
     * Execute migration.
     *
     * @param AbstractMigration $migration
     * @return bool|null|string
     */
    protected function _execute(AbstractMigration $migration)
    {
        $version = $migration->getVersion();
        $migrationName = $this->_plugin . '::' . get_class($migration);

        $sqlInsert = sprintf(
            'INSERT INTO %s ('
            . 'version,'
            . 'migration_name'
            . ') VALUES ('
            . '\'%s\','
            . '\'%s\''
            . ');',
            self::SCHEMA_TABLE,
            $version,
            $migrationName
        );

        $this->_adapter->query($sqlInsert);

        $sqlCheck = sprintf(
            'SELECT version FROM %s WHERE version=\'%s\'',
            self::SCHEMA_TABLE,
            $version
        );

        $versionResult = $this->_adapter->fetchRow($sqlCheck);
        if (count((array) $versionResult) > 0) {
            return __d(
                'core',
                'The version «{0}» of plugin «{1}» has bin success migrated.',
                sprintf('<strong>%s</strong>', $version),
                sprintf('<strong>%s</strong>', __d(Str::low($this->_plugin), $this->_plugin))
            );
        }

        return false;
    }

    /**
     * Get adapter name from application driver.
     *
     * @param string $driver
     * @return string
     */
    protected function _getAdapterName($driver)
    {
        switch ($driver) {
            case 'Cake\Database\Driver\Mysql':
                return 'mysql';

            case 'Cake\Database\Driver\Postgres':
                return 'pgsql';

            case 'Cake\Database\Driver\Sqlite':
                return 'sqlite';

            case 'Cake\Database\Driver\Sqlserver':
                return 'sqlsrv';
        }

        throw new \InvalidArgumentException(__d('core', 'Could not infer database type from driver'));
    }

    /**
     * Get database adapter.
     *
     * @param array $options
     * @return \Phinx\Db\Adapter\AdapterInterface
     */
    protected function _setAdapter(array $options)
    {
        $adapterFactory = AdapterFactory::instance();
        $connection     = new Connection($this->_connectionConfig->getArrayCopy());
        $adapter        = $adapterFactory->getAdapter($this->_adapterName, $options);

        return new CakeAdapter($adapter, $connection);
    }
}
