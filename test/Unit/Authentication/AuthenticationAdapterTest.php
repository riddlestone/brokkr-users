<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Authentication;

use Laminas\Authentication\Result;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Authentication\AuthenticationAdapter;
use Riddlestone\Brokkr\Users\Entity\User;
use Riddlestone\Brokkr\Users\Repository\UserRepository;

class AuthenticationAdapterTest extends TestCase
{
    public function testFailure()
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('findOneByEmailAddressAndPassword')
            ->with('someone@example.com', 'my_password')
            ->willReturn(null);

        $authAdapter = new AuthenticationAdapter($userRepository, 'someone@example.com', 'my_password');
        $result = $authAdapter->authenticate();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isValid());
    }

    public function testSuccess()
    {
        $user = new User();

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('findOneByEmailAddressAndPassword')
            ->with('someone@example.com', 'my_password')
            ->willReturn($user);

        $authAdapter = new AuthenticationAdapter($userRepository, 'someone@example.com', 'my_password');
        $result = $authAdapter->authenticate();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertEquals($user, $result->getIdentity());
    }
}
