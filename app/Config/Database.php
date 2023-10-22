<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations
     * and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to
     * use if no other is specified.
     */
    public string $defaultGroup = 'default';
/*g4tHZUMorC1t_XSZ1If8a_sjnzjAiKgc
 *Server	silly.db.elephantsql.com (silly-01)
Region	amazon-web-services::sa-east-1
Created at	2023-08-18 20:37 UTC+00:00
User & Default database:	khohrezb
 *
 *
 * */
   /* public array $default = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => 'postgres',
        'password' => 'root',
        'database' => 'mural2',
        'DBDriver' => 'Postgre',
        'DBPrefix' => '',
        'pConnect' => true,
        'DBDebug'  => (ENVIRONMENT == 'production'),
        'charset'  => 'utf8',
        'DBCollat' => 'Spanish_Spain.1252',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 5432,
    ];

    public array $default = [
        'DSN'      => '',
        'hostname' => 'silly.db.elephantsql.com',
        'username' => 'khohrezb',
        'password' => 'g4tHZUMorC1t_XSZ1If8a_sjnzjAiKgc',
        'database' => 'khohrezb',
        'DBDriver' => 'Postgre',
        'DBPrefix' => '',
        'pConnect' => true,
        'DBDebug'  => (ENVIRONMENT == 'production'),
        'charset'  => 'utf8',
        'DBCollat' => 'Spanish_Spain.1252',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 5432,
    ];


   */

    /**
     * The default database connection.
     */
    public array $default = [
        'DSN'      => '',
        'hostname' => 'containers-us-west-151.railway.app',
        'username' => 'postgres',
        'password' => 'eUCpUvcid6EpFMbuAVXH',
        'database' => 'railway',
        'DBDriver' => 'Postgre',
        'DBPrefix' => '',
        'pConnect' => true,
        'DBDebug'  => (ENVIRONMENT == 'production'),
        'charset'  => 'utf8',
        'DBCollat' => 'Spanish_Spain.1252',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 6764,
    ];

    /**
     * This database connection is used when
     * running PHPUnit database tests.
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => 'utf8_general_ci',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
    ];

    public function __construct()
    {
        parent::__construct();

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
