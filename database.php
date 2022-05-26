<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'timezone' => '+00:00',
        ],

        //Новый АТС сервер
        'voice_ivr_db' => [
            'driver' => 'pgsql',
            'host' => '37.18.30.15',
            'port' => '5432',
            'database' => 'freeswitchdb',
            'username' => 'voip',
            'password' => 'qld501a87f54',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],

        'call_center' => [
            'driver' => 'pgsql',
            'host' => '78.140.223.180',
            'port' => '5432',
            'database' => 'freeswitchdb',
            'username' => 'postgres',
            'password' => 'gQkjEcpWA65Tfhj',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],

        'asterisk' => [
            'driver' => 'mysql',
            'host' => '78.140.223.32',
            'port' => '3306',
            'database' => 'asteriskcdrdb',
            'username' => 'umarketing',
            'password' => '123102030',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

        'infobank' => [
            'driver' => env('INFOBANK_CONNECTION', 'mysql'),
            'host' => env('INFOBANK_HOST', '37.18.30.17'),
            'port' => env('INFOBANK_PORT', '3306'),
            'database' => env('INFOBANK_DATABASE', 'infobank'),
            'username' => env('INFOBANK_USERNAME', 'marketing'),
            'password' => env('INFOBANK_PASSWORD', 'u96VqBrA'),
            'unix_socket' => env('INFOBANK_SOCKET', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

        'sqlsrv_anviz' => [
            'driver' => 'sqlsrv',
            'host' => 'localhost',
            'port' => '1433',
            'database' => 'bp_anviz',
            'username' => 'SA',
            'password' => 'Asd123102030',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ],

        //Соединение базза данных Bpartners
        'bpartners_db' => [
            'driver' => 'mysql',
            'host' => '185.146.2.237',
            'port' => '3306',
            'database' => 'ci50347_blackbp',
            'username' => 'ci50347_blackbp',
            'password' => 'KN8dH7u2',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],

        //Соединение базза данных Bpartners
        'localbp' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3360',
            'database' => 'base4',
            'username' => 'root',
            'password' => 'admin',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],

        'mediasend_db' => [
            'driver' => 'mysql',
            'host' => '92.53.96.115',
            'port' => '3306',
            'database' => 'ci50347_newumark',
            'username' => 'ci50347_newumark',
            'password' => 'OsxvyQ',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],

        'callibro' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_CALLIBRO'),
            'port' => '3306',
            'database' => env('DB_DATABASE_CALLIBRO'),
            'username' => env('DB_USERNAME_CALLIBRO'),
            'password' => env('DB_PASSWORD_CALLIBRO'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ],


    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
