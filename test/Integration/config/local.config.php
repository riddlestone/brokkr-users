<?php

use Doctrine\DBAL\Driver\PDOSqlite\Driver;
use Laminas\Router\Http\Literal;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => Driver::class,
                'params' => [
                    'memory' => true,
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'name' => 'home',
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                ],
            ],
        ],
    ],
];
