<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Repository\UserRepository;

class UserRepositoryTest extends TestCase
{
    public function testGlobalSaltMethods()
    {
        $entityManager = $this->createMock(EntityManager::class);
        $classMetaData = $this->createMock(ClassMetadata::class);

        $repository = new UserRepository($entityManager, $classMetaData);

        $this->assertNull($repository->getGlobalSalt());

        $repository->setGlobalSalt('GLOBAL_SALT_VALUE');

        $this->assertEquals('GLOBAL_SALT_VALUE', $repository->getGlobalSalt());
    }

    public function testFindByEmailAddressAndPasswordWithIncorrectEmailAddress()
    {
        $repository = $this->createPartialMock(UserRepository::class, ['findOneBy']);
        $repository->setGlobalSalt('GLOBAL_SALT_VALUE');
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['emailAddress' => 'someone.else@example.com'])
            ->willReturn(null);

        $this->assertEquals(
            null,
            $repository->findOneByEmailAddressAndPassword('someone.else@example.com', 'my_password')
        );
    }

    public function testFindByEmailAddressAndPasswordWithIncorrectPassword()
    {
        $user = new User();
        $user->setPassword('my_password', 'GLOBAL_SALT_VALUE');

        $repository = $this->createPartialMock(UserRepository::class, ['findOneBy']);
        $repository->setGlobalSalt('GLOBAL_SALT_VALUE');
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['emailAddress' => 'someone@example.com'])
            ->willReturn($user);

        $this->assertEquals(
            null,
            $repository->findOneByEmailAddressAndPassword('someone@example.com', 'not_my_password')
        );
    }

    public function testFindByEmailAddressAndPasswordSuccess()
    {
        $user = new User();
        $user->setPassword('my_password', 'GLOBAL_SALT_VALUE');

        $repository = $this->createPartialMock(UserRepository::class, ['findOneBy']);
        $repository->setGlobalSalt('GLOBAL_SALT_VALUE');
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['emailAddress' => 'someone@example.com'])
            ->willReturn($user);

        $this->assertEquals($user, $repository->findOneByEmailAddressAndPassword('someone@example.com', 'my_password'));
    }
}
