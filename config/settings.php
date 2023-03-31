<?php

const APP_ROOT = __DIR__ . '/..';

// Bootstrap .env file so we can inject database information without code changes
$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->load();

$isDevelopmentServer = $_ENV['ENVIRONMENT'] !== 'production';

/**
 * Recommended Settings configuration from "Using Doctrine with Slim"
 * @link https://www.slimframework.com/docs/v4/cookbook/database-doctrine.html
 */
return [
    'settings' => [
        'slim' => [
            // Display full error details in response body if we aren't in production
            'displayErrorDetails' => $isDevelopmentServer,

            'logErrors' => true,

            // Displays full PHP error when true, or just Slim application error when false.
            // Only matters when LogErrors is true
            'logErrorDetails' => true,
        ],

        'doctrine' => [
            'connection' => [
                'driver' => 'pdo_mysql',
                'host' => $_ENV['DB_HOST'],
                'port' => $_ENV['DB_PORT'],
                'dbname' => $_ENV['DB_NAME'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASSWORD'],
            ],

            'dev_mode' => $isDevelopmentServer,

            'metadata_directories' => [APP_ROOT . '/src/Domain/Models'],

            'migrations' => [
                'migrations_paths' => [
                    'App\Migrations' => APP_ROOT . '/migrations',
                ],

                'table_storage' => [
                    'table_name' => 'migrations',
                    'version_column_name' => 'version',
                    'version_column_length' => 191,
                    'executed_at_column_name' => 'executed_at',
                    'execution_time_column_name' => 'execution_time',
                ],
            ],
        ]
    ]
];

