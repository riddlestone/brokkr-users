<?php

use Laminas\Router\Http\Literal;

return [
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
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../views',
        ],
    ],
];
