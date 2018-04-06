<?php

return [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header

    // Monolog settings
    'logger' => [
        'name' => 'bzflag-api',
        'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../var/logs/app.log',
        'level' => \Monolog\Logger::DEBUG,
    ],

    'database' => [
        'host' => 'localhost',
        'database' => '',
        'username' => '',
        'password' => '',
    ],

    'legacy_database' => [
        'host' => 'localhost',
        'database' => '',
        'username' => '',
        'password' => ''
    ],

    'passwords' => [
        'options' => [
            'cost' => 12
        ]
    ],
];
