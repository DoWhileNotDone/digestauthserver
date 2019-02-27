<?php
return [
    'settings' => [
        'displayErrorDetails' => getenv('DEBUG') ?? true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'determineRouteBeforeAppMiddleware' => true,
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
            'cache_path' => getenv('DEBUG') ? false : __DIR__ . '/../var/cache/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => getenv('docker') ? 'php://stdout' : __DIR__ . '/../var/logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'database' => [
            'driver' => getenv("DB_DRIVER"),
            'host' => getenv("DB_HOST"),
            'database' => getenv("DB_NAME"),
            'username' => getenv("DB_USER"),
            'password' => getenv("DB_PASSWORD"),
            'schema'    => getenv("DB_SCHEMA"),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
    ],
];
