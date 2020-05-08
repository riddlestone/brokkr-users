<?php

namespace Riddlestone\Brokkr\Users\Acl;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Repository\UserRepository;

class RoleFactory implements AbstractFactoryInterface
{
    /**
     * @param string $requestedName
     * @return string|null
     */
    protected function extractUserId($requestedName)
    {
        $params = explode(':', $requestedName);
        if (count($params) !== 2) {
            return null;
        }
        if ($params[0] !== User::class) {
            return null;
        }
        return $params[1];
    }

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return $this->extractUserId($requestedName) !== null;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $id = $this->extractUserId($requestedName);

        if ($id === null) {
            throw new ServiceNotCreatedException();
        }

        /** @var UserRepository $userRepo */
        $userRepo = $container->get(UserRepository::class);

        $user = $userRepo->find($id);

        if (!$user) {
            throw new ServiceNotFoundException();
        }

        return $user;
    }
}
