<?php

namespace Riddlestone\Brokkr\Users;

use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'acl_resource_manager' => [
        'abstract_factories' => [
            Acl\ResourceFactory::class,
        ],
    ],
    'acl_role_manager' => [
        'abstract_factories' => [
            Acl\RoleFactory::class,
        ],
    ],
    'acl_role_relationship_manager' => [
        'factories' => [
            Acl\RoleRelationshipFactory::class => InvokableFactory::class,
        ],
        'providers' => [
            Acl\RoleRelationshipFactory::class,
        ],
    ],
    'acl_rule_manager' => [
        'factories' => [
            Acl\RuleProvider::class => InvokableFactory::class,
        ],
        'providers' => [
            Acl\RuleProvider::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AccountController::class => Controller\AccountControllerFactory::class,
            Controller\UsersController::class => Controller\UsersControllerFactory::class,
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
    'router' => require __DIR__ . '/module.routes.php',
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => InvokableFactory::class,
            Service\PasswordResetService::class => Service\PasswordResetServiceFactory::class,
            Repository\PasswordResetRepository::class => Repository\RepositoryFactory::class,
            Repository\UserRepository::class => Repository\UserRepositoryFactory::class,
        ],
    ],
];
