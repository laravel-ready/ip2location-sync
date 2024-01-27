<?php

return [
    /**
     * Ip2Location token
     */
    'token' => env('IP2LOCATION_TOKEN'),

    /**
     * Ip type
     * 
     * values:
     *  - IPV4
     *  - IPV6
     * 
     * default: IPV6
     */
    'ip_type' => env('IP2LOCATION_IP_TYPE', 'IPV6'),

    /**
     * Database configuration
     */
    'database' => [
        'driver' => 'mysql',
        'host' => env('IP2LOCATION_MYSQL_HOST', '127.0.0.1'),
        'port' => env('IP2LOCATION_MYSQL_PORT', '1010'),
        'database' => env('IP2LOCATION_MYSQL_DBNAME', 'ip2location_database'),
        'username' => env('IP2LOCATION_MYSQL_USERNAME', 'admin'),
        'password' => env('IP2LOCATION_MYSQL_PASSWORD', 'secret'),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ]
];
