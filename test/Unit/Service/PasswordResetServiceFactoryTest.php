<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Service;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\Router\RouteStackInterface;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Mail\MessageFactory;
use Riddlestone\Brokkr\Users\Repository\PasswordResetRepository;
use Riddlestone\Brokkr\Users\Service\PasswordResetService;
use Riddlestone\Brokkr\Users\Service\PasswordResetServiceFactory;

class PasswordResetServiceFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $entityManager = $this->createMock(EntityManager::class);
        $repository = $this->createMock(PasswordResetRepository::class);
        $messageFactory = $this->createMock(MessageFactory::class);
        $transport = $this->createMock(TransportInterface::class);
        $router = $this->createMock(RouteStackInterface::class);
        $config = ['global_salt' => 'GLOBAL_SALT_VALUE'];

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnMap([
            [EntityManager::class, $entityManager],
            [PasswordResetRepository::class, $repository],
            [MessageFactory::class, $messageFactory],
            [TransportInterface::class, $transport],
            ['Router', $router],
            ['Config', $config],
        ]);

        $factory = new PasswordResetServiceFactory();
        $service = $factory($container, PasswordResetService::class);

        $this->assertInstanceOf(PasswordResetService::class, $service);
        $this->assertEquals($entityManager, $service->getEntityManager());
        $this->assertEquals($repository, $service->getPasswordResetsRepo());
        $this->assertEquals($messageFactory, $service->getMessageFactory());
        $this->assertEquals($transport, $service->getMailTransport());
        $this->assertEquals($router, $service->getRouter());
    }
}
