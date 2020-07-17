<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Entity;

use DateTimeImmutable;
use Riddlestone\Brokkr\Users\Entity\PasswordReset;
use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Entity\User;

class PasswordResetTest extends TestCase
{
    public function testId()
    {
        $passwordReset = new PasswordReset();
        $this->assertNull($passwordReset->getId());
    }

    public function testValidUntil()
    {
        $passwordReset = new PasswordReset();
        $this->assertNull($passwordReset->getValidUntil());
        $passwordReset->setValidUntil($validUntil = new DateTimeImmutable());
        $this->assertEquals($validUntil, $passwordReset->getValidUntil());
    }

    public function testUser()
    {
        $passwordReset = new PasswordReset();
        $this->assertNull($passwordReset->getUser());
        $passwordReset->setUser($user = new User());
        $this->assertEquals($user, $passwordReset->getUser());
    }
}
