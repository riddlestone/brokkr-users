<?php

namespace Riddlestone\Brokkr\Users\Acl;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Repository\UserRepository;

class RoleFactory implements AbstractFactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return object|null
     */
    protected function getUserFromRequestedName(ContainerInterface $container, string $requestedName)
    {
        $validStart = User::class . ':';
        if (substr($requestedName, 0, strlen($validStart)) !== $validStart) {
            return null;
        }
        /** @var UserRepository $userRepo */
        $userRepo = $container->get(UserRepository::class);
        $id = substr($requestedName, strlen($validStart));
        return $userRepo->find($id);
    }

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return (bool)$this->getUserFromRequestedName($container, $requestedName);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $user = $this->getUserFromRequestedName($container, $requestedName);
        if (! $user) {
            throw new ServiceNotFoundException();
        }
        return $user;
    }
}
