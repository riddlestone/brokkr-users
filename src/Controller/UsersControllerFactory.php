<?php

namespace Riddlestone\Brokkr\Users\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Riddlestone\Brokkr\Users\Repository\UserRepository;

class UsersControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UsersController(
            $container->get(UserRepository::class)
        );
    }
}
