<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Repository;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Repository\UserRepository;
use Riddlestone\Brokkr\Users\Repository\UserRepositoryFactory;

class UserRepositoryFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function testInvokeSuccess()
    {
        $repository = $this->createMock(UserRepository::class);
        $repository
            ->expects($this->once())
            ->method('setGlobalSalt')
            ->with('GLOBAL_SALT_VALUE');

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnMap([
            ['Config', ['global_salt' => 'GLOBAL_SALT_VALUE']],
            [EntityManager::class, $entityManager],
        ]);

        $factory = new UserRepositoryFactory();
        $userRepository = $factory($container, UserRepository::class);
        $this->assertEquals($repository, $userRepository);
    }
}
