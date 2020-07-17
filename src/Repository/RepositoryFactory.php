<?php

namespace Riddlestone\Brokkr\Users\Repository;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        return $entityManager->getRepository(
            preg_replace('/\\\\Repository\\\\(.*)Repository$/', '\\Entity\\\$1', $requestedName)
        );
    }
}
