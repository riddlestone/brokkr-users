<?php

namespace Riddlestone\Brokkr\Users;

use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
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
            Service\PasswordResetService::class => Service\PasswordResetServiceFactory::class,
            Repository\PasswordResetRepository::class => Repository\RepositoryFactory::class,
            Repository\UserRepository::class => Repository\UserRepositoryFactory::class,
        ],
    ],
];
