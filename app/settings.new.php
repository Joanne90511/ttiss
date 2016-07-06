<?php
return [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
        'base_url' => 'http://skely.local/form',

        // View settings
        'view' => [
            'template_path' => __DIR__ . '/templates',
            'twig' => [
                'cache' => __DIR__ . '/../cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../log/app.log',
        ],

        //REDCap API Settings
        'api' => [
            'url' => '',
            'project_id' => 0,
        ],

        //Database Details
        'db'                  => [
            'host'     => '',
            'user'     => '',
            'password' => '',
            'database' => '',
        ],
    ],
];
