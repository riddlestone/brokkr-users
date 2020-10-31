<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Entity;

use Exception;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Entity\User;

class UserTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testId()
    {
        $user = new User();
        $this->assertNull($user->getId());
        $this->expectExceptionMessage('Cannot get role ID of User before they have an ID');
        $user->getRoleId();
    }

    public function namesData()
    {
        return [
            [
                'first_name' => null,
                'last_name' => null,
                'name' => null,
            ],
            [
                'first_name' => 'My',
                'last_name' => null,
                'name' => 'My',
            ],
            [
                'first_name' => null,
                'last_name' => 'Name',
                'name' => 'Name',
            ],
            [
                'first_name' => 'My',
                'last_name' => 'Name',
                'name' => 'My Name',
            ],
        ];
    }

    /**
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $name
     * @dataProvider namesData
     */
    public function testNames($firstName, $lastName, $name)
    {
        $user = new User();
        if ($firstName !== null) {
            $user->setFirstName($firstName);
        }
        if ($lastName !== null) {
            $user->setLastName($lastName);
        }
        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
        $this->assertEquals($name, $user->getName());
    }

    public function testEmailAddress()
    {
        $user = new User();
        $this->assertNull($user->getEmailAddress());
        $user->setEmailAddress('user@example.com');
        $this->assertEquals('user@example.com', $user->getEmailAddress());
    }

    public function testPasswordSalt()
    {
        $user = new User();
        $this->assertIsString($user->getPasswordSalt());
        $this->assertNotEquals('SomeOtherSalt', $user->getPasswordSalt());
        $user->setPasswordSalt('SomeOtherSalt');
        $this->assertEquals('SomeOtherSalt', $user->getPasswordSalt());
    }

    public function testPasswordHash()
    {
        $user = new User();
        $this->assertNull($user->getPasswordHash());
        $user->setPasswordHash('SomeHash');
        $this->assertEquals('SomeHash', $user->getPasswordHash());
    }

    public function testPassword()
    {
        $user1 = new User();
        $user1->setPassword('password', 'GlobalSalt');
        $this->assertTrue($user1->checkPassword('password', 'GlobalSalt'));
        $this->assertFalse($user1->checkPassword('another-password', 'GlobalSalt'));
        $this->assertFalse($user1->checkPassword('password', 'AnotherSalt'));

        $user2 = new User();
        $user2->setPassword('password', 'GlobalSalt');
        $this->assertTrue($user1->checkPassword('password', 'GlobalSalt'));

        $this->assertNotEquals($user1->getPasswordHash(), $user2->getPasswordHash());
    }
}
