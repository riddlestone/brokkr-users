<?php

return [
    'modules' => [
        'Laminas\Session',
        'Laminas\Form',
        'Laminas\Router',
        'DoctrineModule',
        'DoctrineORMModule',
        'Riddlestone\Brokkr\Acl',
        'Riddlestone\Brokkr\Mail',
        'Riddlestone\Brokkr\Users',
    ],
    'module_listener_options' => [
        'use_laminas_loader' => false,
        'config_glob_paths' => [
            realpath(__DIR__) . '/local.config.php',
        ],
        'config_cache_enabled' => false,
        'module_map_cache_enabled' => false,
    ],
];
