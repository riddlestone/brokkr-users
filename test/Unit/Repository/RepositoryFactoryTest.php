<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Repository\RepositoryFactory;

class RepositoryFactoryTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function testInvokeSuccess()
    {
        $repository = $this->createMock(EntityRepository::class);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with('Some\\Entity\\Thing')
            ->willReturn($repository);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(function ($name) use ($entityManager) {
            return $name === EntityManager::class ? $entityManager : null;
        });

        $factory = new RepositoryFactory();
        $this->assertEquals($repository, $factory($container, 'Some\\Repository\\ThingRepository'));
    }
}
