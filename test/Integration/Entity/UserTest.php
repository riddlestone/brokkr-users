<?php

namespace Riddlestone\Brokkr\Users\Test\Integration\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Test\Integration\AbstractApplicationTestCase;

class UserTest extends AbstractApplicationTestCase
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testId()
    {
        /** @var EntityManager $em */
        $em = $this->app->getServiceManager()->get(EntityManager::class);
        $user = new User();
        $this->assertNull($user->getId());

        $user->setFirstName('My');
        $user->setLastName('Name');
        $user->setEmailAddress('someone@example.com');
        $user->setPassword('password', $this->app->getServiceManager()->get('Config')['global_salt']);

        $em->persist($user);
        $em->flush();
        $this->assertIsString($user->getId());
    }

    /**
     * @throws Exception
     */
    public function testEarlyRoleId()
    {
        $user = new User();
        $this->expectException(Exception::class);
        $user->getRoleId();
    }

    public function testRoleId()
    {
        /** @var EntityManager $em */
        $em = $this->app->getServiceManager()->get(EntityManager::class);
        $user = new User();
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setEmailAddress('someone@example.com');
        $user->setPassword('password', $this->app->getServiceManager()->get('Config')['global_salt']);
        $em->persist($user);
        $em->flush();
        $this->assertIsString($user->getRoleId());
        $this->assertStringStartsWith(User::class . ':', $user->getRoleId());
    }
}
