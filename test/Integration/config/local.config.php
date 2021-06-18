<?php

use Doctrine\DBAL\Driver\PDOSqlite\Driver;

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
    'mail' => [
        'transport' => [
            'type' => 'inmemory',
        ],
    ],
];
