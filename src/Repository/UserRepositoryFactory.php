<?php

namespace Riddlestone\Brokkr\Users\Repository;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Riddlestone\Brokkr\Users\Entity\User;

class UserRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        /** @var UserRepository $repository */
        $repository = $entityManager->getRepository(User::class);
        $repository->setGlobalSalt($container->get('Config')['global_salt']);
        return $repository;
    }
}
