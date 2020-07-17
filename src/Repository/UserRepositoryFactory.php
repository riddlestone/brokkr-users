<?php

namespace Riddlestone\Brokkr\Users\Repository;

use Interop\Container\ContainerInterface;

class UserRepositoryFactory extends RepositoryFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var UserRepository $repository */
        $repository = parent::__invoke($container, $requestedName, $options);
        $repository->setGlobalSalt($container->get('Config')['global_salt']);
        return $repository;
    }
}
