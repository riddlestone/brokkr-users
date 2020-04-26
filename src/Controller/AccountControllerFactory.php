<?php

namespace Riddlestone\Brokkr\Users\Controller;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Riddlestone\Brokkr\Users\Repository\UserRepository;

class AccountControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AccountController(
            $container->get(UserRepository::class),
            $container->get(AuthenticationService::class),
            $container->get('FormElementManager')
        );
    }
}
