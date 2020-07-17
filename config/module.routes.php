<?php

namespace Riddlestone\Brokkr\Users;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Placeholder;
use Laminas\Router\Http\Segment;

return [
    'routes' => [
        'home' => [
            'type' => Placeholder::class,
        ],
        'brokkr-users' => [
            'type' => Placeholder::class,
            'child_routes' => [
                'account' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/account',
                        'defaults' => [
                            'controller' => Controller\AccountController::class,
                            'action' => 'index',
                        ],
                    ],
                    'may_terminate' => true,
                    'child_routes' => [
                        'request-password-reset' => [
                            'type' => Literal::class,
                            'options' => [
                                'route' => '/request-password-reset',
                                'defaults' => [
                                    'action' => 'requestPasswordReset',
                                ],
                            ],
                        ],
                        'reset-password' => [
                            'type' => Segment::class,
                            'options' => [
                                'route' => '/reset-password/:id',
                                'defaults' => [
                                    'action' => 'resetPassword',
                                ],
                            ],
                        ],
                        'login' => [
                            'type' => Literal::class,
                            'options' => [
                                'route' => '/login',
                                'defaults' => [
                                    'action' => 'login',
                                ],
                            ],
                        ],
                        'logout' => [
                            'type' => Literal::class,
                            'options' => [
                                'route' => '/logout',
                                'defaults' => [
                                    'action' => 'logout',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
