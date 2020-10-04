<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Controller;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Controller\UsersController;
use Riddlestone\Brokkr\Users\Controller\UsersControllerFactory;
use Riddlestone\Brokkr\Users\Repository\UserRepository;

class UsersControllerFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('get')
            ->willReturnMap([
                [UserRepository::class, $this->createMock(UserRepository::class)],
            ]);

        $factory = new UsersControllerFactory();

        $controller = $factory($container, UsersController::class);

        $this->assertInstanceOf(UsersController::class, $controller);
    }
}
