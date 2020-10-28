<?php

namespace Riddlestone\Brokkr\Users\Acl;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Riddlestone\Brokkr\Acl\GenericResource;
use Riddlestone\Brokkr\Users\Controller\AccountController;
use Riddlestone\Brokkr\Users\Controller\UsersController;

class ResourceFactory implements AbstractFactoryInterface
{
    protected $actions = [
        AccountController::class => ['login', 'logout'],
        UsersController::class => ['index'],
    ];

    /**
     * Can the factory create an instance for the service?
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (strpos($requestedName, '::') === false) {
            return array_key_exists($requestedName, $this->actions);
        }

        list($controller, $action) = explode('::', $requestedName);

        return isset($this->actions[$controller])
            && in_array($action, $this->actions[$controller]);
    }

    /**
     * Create an object
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (strpos($requestedName, '::') === false) {
            return new GenericResource($requestedName);
        }

        list($controller) = explode('::', $requestedName);

        return new GenericResource($requestedName, $controller);
    }
}
