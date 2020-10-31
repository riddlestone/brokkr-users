<?php

namespace Riddlestone\Brokkr\Users\Service;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Riddlestone\Brokkr\Mail\MessageFactory;
use Riddlestone\Brokkr\Users\Repository\PasswordResetRepository;

class PasswordResetServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return PasswordResetService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PasswordResetService(
            $container->get(EntityManager::class),
            $container->get(PasswordResetRepository::class),
            $container->get(MessageFactory::class),
            $container->get(TransportInterface::class),
            $container->get('Router'),
            $container->get('Config')['global_salt']
        );
    }
}
