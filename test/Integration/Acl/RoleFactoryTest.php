<?php

namespace Riddlestone\Brokkr\Users\Test\Integration\Acl;

use Doctrine\ORM\EntityManager;
use Riddlestone\Brokkr\Acl\Acl;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Test\Integration\AbstractApplicationTestCase;

class RoleFactoryTest extends AbstractApplicationTestCase
{
    public function testGetValidUser()
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

        /** @var Acl $acl */
        $acl = $this->app->getServiceManager()->get(Acl::class);
        $user2 = $acl->getRole($user->getRoleId());

        $this->assertEquals($user, $user2);
    }
}
