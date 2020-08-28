<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Controller;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\AbstractPluginManager;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Controller\AccountController;
use Riddlestone\Brokkr\Users\Controller\AccountControllerFactory;
use Riddlestone\Brokkr\Users\Repository\UserRepository;
use Riddlestone\Brokkr\Users\Service\PasswordResetService;

class AccountControllerFactoryTest extends TestCase
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
                [AuthenticationService::class, $this->createMock(AuthenticationService::class)],
                ['FormElementManager', $this->createMock(AbstractPluginManager::class)],
                [PasswordResetService::class, $this->createMock(PasswordResetService::class)],
            ]);

        $factory = new AccountControllerFactory();

        $controller = $factory($container, AccountController::class);

        $this->assertInstanceOf(AccountController::class, $controller);
    }
}
