<?php

namespace Riddlestone\Brokkr\Users;

use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Riddlestone\Brokkr\Users\Acl\RoleFactory;
use Riddlestone\Brokkr\Users\Controller\AccountController;
use Riddlestone\Brokkr\Users\Controller\AccountControllerFactory;
use Riddlestone\Brokkr\Users\Controller\UsersController;
use Riddlestone\Brokkr\Users\Controller\UsersControllerFactory;
use Riddlestone\Brokkr\Users\Repository\UserRepository;
use Riddlestone\Brokkr\Users\Repository\UserRepositoryFactory;

return [
    'acl_role_manager' => [
        'abstract_factories' => [
            RoleFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            AccountController::class => AccountControllerFactory::class,
            UsersController::class => UsersControllerFactory::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            'riddlestone_brokkr_users_driver' => [
                'class' => XmlDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/entities/',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\\Entity' => 'riddlestone_brokkr_users_driver',
                ],
            ],
        ],
    ],
    'global_salt' => 'PLEASE CHANGE ME',
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => InvokableFactory::class,
            UserRepository::class => UserRepositoryFactory::class,
        ],
    ],
];
